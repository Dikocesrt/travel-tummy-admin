<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Buat UUID kalau belum ada
        if (!isset($data['id'])) {
            $data['id'] = Str::uuid()->toString();
        }

        if (isset($data['him_rating']) && isset($data['her_rating'])) {
            $data['overall_rating'] = round(($data['him_rating'] + $data['her_rating']) / 2, 2);
        } elseif (isset($data['her_rating'])) {
            $data['overall_rating'] = $data['her_rating'];
        } elseif (isset($data['him_rating'])) {
            $data['overall_rating'] = $data['him_rating'];
        }

        // Konfigurasi Cloudinary sekali di awal
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => ['secure' => true]
        ]);

        $cloudinary = new Cloudinary();

        if (!empty($data['image_url'])) {
            $relativePath = $data['image_url'];
            $localPath = Storage::disk('public')->path($relativePath);

            if (file_exists($localPath)) {
                try {
                    $uploaded = $cloudinary->uploadApi()->upload($localPath, [
                        'folder' => 'menus',
                    ]);

                    if (isset($uploaded['public_id'])) {
                        $data['image_url'] = $uploaded['public_id'];
                        Storage::disk('public')->delete($relativePath);
                    }
                } catch (\Exception $e) {
                    Log::error('Cloudinary upload failed (image_url)', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::error('File not found (image_url)', ['path' => $localPath]);
            }
        }

        return $data;
    }
}

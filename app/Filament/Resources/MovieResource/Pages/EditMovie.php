<?php

namespace App\Filament\Resources\MovieResource\Pages;

use App\Filament\Resources\MovieResource;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditMovie extends EditRecord
{
    protected static string $resource = MovieResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['image_url'])) {
            unset($data['image_url']);
        }

        if (isset($data['him_rating']) && isset($data['her_rating'])) {
            $data['overall_rating'] = round(($data['him_rating'] + $data['her_rating']) / 2, 2);
        } elseif (isset($data['her_rating'])) {
            $data['overall_rating'] = $data['her_rating'];
        } elseif (isset($data['him_rating'])) {
            $data['overall_rating'] = $data['him_rating'];
        }
        
        if (!empty($data['image_url'])) {
            $relativePath = $data['image_url'];
            $localPath = Storage::disk('public')->path($relativePath);

            if (file_exists($localPath)) {
                try {
                    // KONFIGURASI MANUAL
                    Configuration::instance([
                        'cloud' => [
                            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                            'api_key'    => env('CLOUDINARY_API_KEY'),
                            'api_secret' => env('CLOUDINARY_API_SECRET'),
                        ],
                        'url' => ['secure' => true]
                    ]);

                    $cloudinary = new Cloudinary();

                    $uploaded = $cloudinary->uploadApi()->upload($localPath, [
                        'folder' => 'movies',
                    ]);

                    if (isset($uploaded['public_id'])) {
                        $data['image_url'] = $uploaded['public_id'];
                        Storage::disk('public')->delete($relativePath);
                    }
                } catch (\Exception $e) {
                    Log::error('Cloudinary upload exception', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::error('File not found', ['path' => $localPath]);
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

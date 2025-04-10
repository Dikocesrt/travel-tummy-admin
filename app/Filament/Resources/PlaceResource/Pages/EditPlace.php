<?php

namespace App\Filament\Resources\PlaceResource\Pages;

use App\Filament\Resources\PlaceResource;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditPlace extends EditRecord
{
    protected static string $resource = PlaceResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {   
        if (empty($data['image_url'])) {
            unset($data['image_url']);
        }

        if (empty($data['map_url'])) {
            unset($data['map_url']);
        }

        $data['overall_rating'] = round(($data['him_rating'] + $data['her_rating']) / 2, 2);

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

        // Upload image_url (gambar tempat)
        if (!empty($data['image_url'])) {
            $relativePath = $data['image_url'];
            $localPath = Storage::disk('public')->path($relativePath);

            if (file_exists($localPath)) {
                try {
                    $uploaded = $cloudinary->uploadApi()->upload($localPath, [
                        'folder' => 'places',
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

        // Upload map_url (map tempat)
        if (!empty($data['map_url'])) {
            $relativePath = $data['map_url'];
            $localPath = Storage::disk('public')->path($relativePath);

            if (file_exists($localPath)) {
                try {
                    $uploaded = $cloudinary->uploadApi()->upload($localPath, [
                        'folder' => 'maps',
                    ]);

                    if (isset($uploaded['public_id'])) {
                        $data['map_url'] = $uploaded['public_id'];
                        Storage::disk('public')->delete($relativePath);
                    }
                } catch (\Exception $e) {
                    Log::error('Cloudinary upload failed (map_url)', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::error('File not found (map_url)', ['path' => $localPath]);
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

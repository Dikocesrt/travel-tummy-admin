<?php

namespace App\Filament\Resources\PhotoResource\Pages;

use App\Filament\Resources\PhotoResource;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditPhoto extends EditRecord
{
    protected static string $resource = PhotoResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['image_url'])) {
            unset($data['image_url']);
        }

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
                        'folder' => 'us',
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

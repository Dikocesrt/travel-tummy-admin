<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovieResource\Pages;
use App\Filament\Resources\MovieResource\RelationManagers;
use App\Models\Movie;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MovieResource extends Resource
{
    protected static ?string $model = Movie::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->placeholder("Agak Laen")
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Film'),
                TextInput::make('genre')
                    ->placeholder("Horror, Komedi")
                    ->required()
                    ->maxLength(255)
                    ->label('Genre Film'),
                TextInput::make('origin')
                    ->placeholder("Indonesia")
                    ->required()
                    ->maxLength(255)
                    ->label('Asal Film'),
                TextInput::make('him_rating')
                    ->placeholder("8.5")
                    ->numeric()
                    ->inputMode('decimal')
                    ->label('Rating Diko'),
                TextInput::make('her_rating')
                    ->placeholder("8.5")
                    ->numeric()
                    ->inputMode('decimal')
                    ->label('Rating Kirani'),
                FileUpload::make('image_url')
                    ->maxSize(10240)
                    ->label('Upload Gambar')
                    ->image()
                    ->disk('public') // Simpan sementara di storage/app/public
                    ->directory('movies') // Folder tempat simpan file lokal
                    ->visibility('public')
                    ->required(fn (string $operation) => $operation === 'create')
                    ->preserveFilenames() // Supaya nama file tidak acak
                    ->maxFiles(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Gambar')
                    ->width(200)
                    ->height(200)
                    ->getStateUsing(fn ($record) => 'https://res.cloudinary.com/' . env('CLOUDINARY_CLOUD_NAME') . '/image/upload/' . $record->image_url),
                TextColumn::make('title')
                    ->searchable()
                    ->label('Judul'),
                TextColumn::make('genre')
                    ->searchable()
                    ->label('Genre'),
                TextColumn::make('origin')
                    ->searchable()
                    ->label('Asal'),
                TextColumn::make('him_rating')
                    ->sortable()
                    ->label('Rating Diko')
                    ->badge()
                    ->color(function ($record) {
                        $rating = $record->him_rating;
                
                        if ($rating < 5) {
                            return 'danger'; // merah
                        } elseif ($rating < 7.5) {
                            return 'warning'; // kuning
                        } else {
                            return 'success'; // hijau
                        }
                    }),
                TextColumn::make('her_rating')
                    ->sortable()
                    ->label('Rating Kirani')
                    ->badge()
                    ->color(function ($record) {
                        $rating = $record->her_rating;
                
                        if ($rating < 5) {
                            return 'danger'; // merah
                        } elseif ($rating < 7.5) {
                            return 'warning'; // kuning
                        } else {
                            return 'success'; // hijau
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMovies::route('/'),
            'create' => Pages\CreateMovie::route('/create'),
            'edit' => Pages\EditMovie::route('/{record}/edit'),
        ];
    }

    protected static function afterCreate($record): void
    {
        Log::info('⚡️ afterCreate hook fired', ['record_id' => $record->id]);
        static::processCloudinaryUpload($record);
    }

    protected static function afterSave($record): void
    {
        Log::info('⚡️ afterSave hook fired', ['record_id' => $record->id]);
        static::processCloudinaryUpload($record);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaceResource\Pages;
use App\Filament\Resources\PlaceResource\RelationManagers;
use App\Models\Place;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlaceResource extends Resource
{
    protected static ?string $model = Place::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->placeholder("Kopi Kenangan Jakal")
                    ->required()
                    ->label("Nama Tempat")
                    ->maxLength(255),
                Select::make('room')
                    ->label('Lebar Tempat')
                    ->required()
                    ->options([
                        'sempit' => 'Sempit',
                        'biasa' => 'Biasa',
                        'luas' => 'Luas',
                    ]),
                Select::make('parking')
                    ->label('Parkir')
                    ->required()
                    ->options([
                        'free' => 'Free',
                        'bayar' => 'Bayar',
                    ]),
                Select::make('wifi')
                    ->label('Wifi Status')
                    ->required()
                    ->options([
                        'lambat' => 'Lambat',
                        'biasa' => 'Biasa',
                        'lancar' => 'Lancar',
                    ]),
                TextInput::make('open_hour')
                    ->placeholder("00:00")
                    ->required()
                    ->label("Jam Buka")
                    ->maxLength(255),
                TextInput::make('close_hour')
                    ->placeholder("00:00")
                    ->required()
                    ->label("Jam Tutup")
                    ->maxLength(255),
                TextInput::make('price_min')
                    ->placeholder("15000")
                    ->required()
                    ->label("Harga Paling Murah")
                    ->numeric(),
                TextInput::make('price_max')
                    ->placeholder("15000")
                    ->required()
                    ->label("Harga Paling Mahal")
                    ->numeric(),
                TextInput::make('him_rating')
                    ->placeholder("8.2")
                    ->required()
                    ->label("Rating Diko")
                    ->numeric()
                    ->inputMode('decimal')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $her = $get('her_rating');
                        if (!empty($her)) {
                            $set('overall_rating', ($state + $her) / 2);
                        } else {
                            $set('overall_rating', $state);
                        }
                    }),
                TextInput::make('her_rating')
                    ->placeholder("8.2")
                    ->required()
                    ->label("Rating Kirani")
                    ->numeric()
                    ->inputMode('decimal')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $him = $get('him_rating');
                        if (!empty($him)) {
                            $set('overall_rating', ($state + $him) / 2);
                        } else {
                            $set('overall_rating', $state);
                        }
                    }),
                TextInput::make('overall_rating')
                    ->required()
                    ->label("Rating Total")
                    ->numeric()
                    ->inputMode('decimal')
                    ->disabled(),
                Toggle::make('is_fav')
                    ->required()
                    ->label('Favorit')
                    ->onColor('success')
                    ->offColor('danger'),
                FileUpload::make('image_url')
                    ->maxSize(10240)
                    ->label('Upload Gambar Tempat')
                    ->image()
                    ->disk('public')
                    ->directory('places')
                    ->visibility('public')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->preserveFilenames()
                    ->maxFiles(1),
                FileUpload::make('map_url')
                    ->maxSize(10240)
                    ->label('Upload Map Tempat')
                    ->image()
                    ->disk('public')
                    ->directory('maps')
                    ->visibility('public')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->preserveFilenames()
                    ->maxFiles(1),
                MarkdownEditor::make('description')
                    ->columnSpan('full')
                    ->required()
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'heading',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'table',
                        'undo',
                    ])
                    ->label("Deskripsi Tempat")
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
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Tempat'),
                TextColumn::make('description')
                    ->wrap()
                    ->limit(50)
                    ->label('Deskripsi'),
                SelectColumn::make('parking')
                    ->label('Parkir')
                    ->options([
                        'free' => 'Free',
                        'bayar' => 'Bayar',
                    ]),
                SelectColumn::make('wifi')
                    ->label('Wifi Status')
                    ->options([
                        'lambat' => 'Lambat',
                        'biasa' => 'Biasa',
                        'lancar' => 'Lancar',
                    ]),
                SelectColumn::make('room')
                    ->label('Tempat')
                    ->options([
                        'sempit' => 'Sempit',
                        'luas' => 'Luas',
                    ]),
                TextColumn::make('open_hour')
                    ->label('Jam Buka'),
                TextColumn::make('close_hour')
                    ->label('Jam Tutup'),
                TextColumn::make('price_min')
                    ->sortable()
                    ->label('Harga Paling Murah'),
                TextColumn::make('price_max')
                    ->sortable()
                    ->label('Harga Paling Mahal'),
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
                TextColumn::make('overall_rating')
                    ->sortable()
                    ->label('Rating Total')
                    ->badge()
                    ->color(function ($record) {
                        $rating = $record->overall_rating;
                
                        if ($rating < 5) {
                            return 'danger'; // merah
                        } elseif ($rating < 7.5) {
                            return 'warning'; // kuning
                        } else {
                            return 'success'; // hijau
                        }
                    }),
                ToggleColumn::make('is_fav')
                    ->label('Favorit'),
                ImageColumn::make('map_url')
                    ->label('Map')
                    ->width(350)
                    ->height(100)
                    ->getStateUsing(fn ($record) => 'https://res.cloudinary.com/' . env('CLOUDINARY_CLOUD_NAME') . '/image/upload/' . $record->map_url),
                
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
            'index' => Pages\ListPlaces::route('/'),
            'create' => Pages\CreatePlace::route('/create'),
            'edit' => Pages\EditPlace::route('/{record}/edit'),
        ];
    }
}

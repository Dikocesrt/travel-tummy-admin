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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlaceResource extends Resource
{
    protected static ?string $model = Place::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                        if ($her !== null) {
                            $set('overall_rating', ($state + $her) / 2);
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
                        if ($him !== null) {
                            $set('overall_rating', ($state + $him) / 2);
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
                    ->label('Upload Gambar Tempat')
                    ->image()
                    ->disk('public')
                    ->directory('places')
                    ->visibility('public')
                    ->required()
                    ->preserveFilenames()
                    ->maxFiles(1),
                FileUpload::make('map_url')
                    ->label('Upload Map Tempat')
                    ->image()
                    ->disk('public')
                    ->directory('maps')
                    ->visibility('public')
                    ->required()
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
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

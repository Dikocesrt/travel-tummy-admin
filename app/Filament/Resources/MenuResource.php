<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
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

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->placeholder("Nasi Goreng Telur")
                    ->required()
                    ->label("Nama Menu")
                    ->maxLength(255),
                Select::make('portion')
                    ->label('Porsi Menu')
                    ->required()
                    ->options([
                        'cemil' => 'cemil',
                        'lumayan' => 'lumayan',
                        'ngenyangin' => 'ngenyangin',
                        'banyak' => 'banyak',
                        'super' => 'super',
                    ]),
                TextInput::make('price')
                    ->placeholder("15000")
                    ->required()
                    ->label("Harga Menu")
                    ->numeric(),
                FileUpload::make('image_url')
                    ->maxSize(10240)
                    ->label('Upload Gambar Menu')
                    ->image()
                    ->disk('public')
                    ->directory('menus')
                    ->visibility('public')
                    ->required(fn (string $operation) => $operation === 'create')
                    ->preserveFilenames()
                    ->maxFiles(1),
                TextInput::make('him_rating')
                    ->placeholder("8.2")
                    ->label("Rating Diko")
                    ->numeric(),
                TextInput::make('her_rating')
                    ->placeholder("8.2")
                    ->label("Rating Kirani")
                    ->numeric(),
                Toggle::make('is_fav')
                    ->required()
                    ->label('Favorit')
                    ->onColor('success')
                    ->offColor('danger'),
                Select::make('place_id')
                    ->label('Tempat')
                    ->required()
                    ->options(Place::all()->pluck('name', 'id'))
                    ->searchable(),
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
                    ->label("Deskripsi Menu")
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->sortable()
                    ->label('Waktu Input')
                    ->dateTime(),
                ImageColumn::make('image_url')
                    ->label('Gambar')
                    ->width(200)
                    ->height(200)
                    ->getStateUsing(fn ($record) => 'https://res.cloudinary.com/' . env('CLOUDINARY_CLOUD_NAME') . '/image/upload/' . $record->image_url),
                SelectColumn::make('place_id')
                    ->sortable()
                    ->searchable()
                    ->label('Tempat')
                    ->options(Place::all()->pluck('name', 'id')),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Menu'),
                TextColumn::make('description')
                    ->wrap()
                    ->limit(50)
                    ->label('Deskripsi'),
                TextColumn::make('price')
                    ->sortable()
                    ->label('Harga'),
                SelectColumn::make('portion')
                    ->label('Porsi Menu')
                    ->options([
                        'cemil' => 'cemil',
                        'lumayan' => 'lumayan',
                        'ngenyangin' => 'ngenyangin',
                        'banyak' => 'banyak',
                        'super' => 'super',
                    ]),
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}

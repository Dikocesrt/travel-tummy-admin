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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->label('Upload Gambar Menu')
                    ->image()
                    ->disk('public')
                    ->directory('menus')
                    ->visibility('public')
                    ->required()
                    ->preserveFilenames()
                    ->maxFiles(1),
                TextInput::make('him_rating')
                    ->placeholder("8.2")
                    ->label("Rating Diko")
                    ->numeric()
                    ->inputMode('decimal')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $her = $get('her_rating');
                        if ($her == null) {
                            $set('overall_rating', $state);
                        } else {
                            $set('overall_rating', ($state + $her) / 2);
                        }
                    }),
                TextInput::make('her_rating')
                    ->placeholder("8.2")
                    ->label("Rating Kirani")
                    ->numeric()
                    ->inputMode('decimal')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $him = $get('him_rating');
                        if ($him !== null) {
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}

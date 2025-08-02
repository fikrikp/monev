<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationLabel = 'Area Management';


    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_area')
                    ->label('Area')
                    ->relationship('area', 'area_name')
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('room_id', null))
                    ->createOptionForm([
                        TextInput::make('area_name')
                            ->label('Nama Area')
                            ->required()
                            ->maxLength(255),
                    ]),
                TextInput::make('room_name')
                    ->label('Nama Ruangan')
                    ->required()
                    ->maxLength(255),
                Select::make('categories')
                    ->label('Kategori Barang')
                    ->multiple()
                    ->relationship('categories', 'category_name')
                    ->preload()
                    ->required(),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label('No'),
                TextColumn::make('area.area_name')->label('Nama Area')->sortable()->searchable(),
                TextColumn::make('room_name')->label('Nama Ruangan')->sortable()->searchable(),
                TextColumn::make('categories.category_name')
                    ->label('Kategori')
                    ->badge()
                    ->limitList(3)
                    ->separator(', ')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
        ];
    }
}

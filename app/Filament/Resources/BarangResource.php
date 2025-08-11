<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Models\Barang;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Barang Inventaris';
    protected static ?string $pluralModelLabel = 'Barang Inventaris';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('item_name')
                    ->label('Nama Barang')
                    ->required()
                    ->maxLength(255),
                TextInput::make('type')
                    ->label('Type')
                    ->required()
                    ->maxLength(255),
                Select::make('area_id')
                    ->label('Area')
                    ->options(Area::all()->pluck('area_name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('room_id', null))
                    ->native(false),
                Select::make('room_id')
                    ->label('Ruangan')
                    ->options(function (callable $get) {
                        $areaId = $get('area_id');
                        if (!$areaId) {
                            return [];
                        }
                        return \App\Models\Room::where('id_area', $areaId)->pluck('room_name', 'id');
                    })
                    ->required()
                    ->native(false),
                Select::make('category_id')
                    ->label('Kategori')
                    ->options(function (callable $get) {
                        $roomId = $get('room_id');
                        if (!$roomId) {
                            return [];
                        }

                        $room = \App\Models\Room::with('categories')->find($roomId);
                        if (!$room) {
                            return [];
                        }

                        return $room->categories->pluck('category_name', 'id')->toArray();
                    })
                    ->required()
                    ->native(false),

                Select::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'rusak' => 'Rusak',
                    ])
                    ->required()
                    ->native(false),
            ]);
    }

    // ...
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label('No'),
                TextColumn::make('item_name')
                    ->label('Nama Barang')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('room.area.area_name')
                    ->label('Area')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('room.room_name')
                    ->label('Ruangan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak' => 'danger',
                    })->sortable()->searchable(),
            ])

            ->filters([
                SelectFilter::make('area')
                    ->label('Area')
                    ->options(Area::all()->pluck('area_name', 'area_name'))
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('room.area', function ($q) use ($data) {
                                $q->where('area_name', $data['value']);
                            });
                        }
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Tambahkan ini
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Barang')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('item_name')->label('Nama Barang'),
                        TextEntry::make('type')->label('Type'),
                        TextEntry::make('category.category_name')->label('Kategori'),
                    ]),

                Section::make('Kondisi per Ruangan')
                    ->schema([
                        TextEntry::make('room.room_name')
                            ->label('Ruangan'),
                        TextEntry::make('condition')
                            ->label('Kondisi')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'baik' => 'success',
                                'rusak' => 'danger',
                            }),
                    ])
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
            'view' => Pages\ViewBarang::route('/{record}'),
        ];
    }
}

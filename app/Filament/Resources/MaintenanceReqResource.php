<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceReqResource\Pages;
use App\Models\MaintenanceReq;
use App\Models\User;
use App\Models\Barang;
use App\Models\Area;
use App\Models\Room;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;

class MaintenanceReqResource extends Resource
{
    protected static ?string $model = MaintenanceReq::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?int $navigationSort = 4;

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::check() ? Auth::id() : null;
        $data['nama_staff'] = Auth::check() ? Auth::user()->name : null;
        return $data;
    }

    // ✅ Metode untuk memfilter record yang ditampilkan di tabel
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Filter umum: Tampilkan yang statusnya bukan 'done' atau yang statusnya masih NULL
        // Ini memastikan Chief Engineer melihat semua yang masih perlu ditindaklanjuti
        $query->where(function (Builder $q) {
            $q->where('status', '!=', 'done')
                ->orWhereNull('status');
        });

        // Filter spesifik untuk Staff: hanya tampilkan request yang mereka buat sendiri
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'staff') {
            $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ✅ Field user_id disembunyikan dan diisi otomatis
                Hidden::make('user_id')
                    ->default(fn() => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null),

                // ✅ Field nama_staff: Dibuat hidden dan read-only untuk staff
                TextInput::make('nama_staff')
                    ->label('Nama Staff')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->hidden(fn() => \Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'staff'),

                Select::make('id_area')
                    ->label('Area')
                    ->options(Area::all()->pluck('area_name', 'id'))
                    ->searchable()
                    ->afterStateUpdated(fn(callable $set) => $set('room_id', null))
                    ->noSearchResultsMessage('Data tidak ditemukan')
                    ->reactive(),

                Select::make('room_id')
                    ->label('Ruangan')
                    ->options(function (callable $get) {
                        $areaId = $get('id_area');
                        if (!$areaId) return [];
                        return Room::where('id_area', $areaId)->pluck('room_name', 'id');
                    })
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('category_id', null))
                    ->noSearchResultsMessage('Data tidak ditemukan'),

                Select::make('category_id')
                    ->label('Kategori')
                    ->options(function (callable $get) {
                        $roomId = $get('room_id');
                        if (!$roomId) return [];

                        $room = \App\Models\Room::with('categories')->find($roomId);
                        if (!$room) return [];

                        return $room->categories->pluck('category_name', 'id');
                    })
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('id_barang', null))
                    ->noSearchResultsMessage('Kategori tidak tersedia untuk ruangan ini'),

                Select::make('id_barang')
                    ->label('Barang')
                    ->options(function (callable $get) {
                        $roomId = $get('room_id');
                        $categoryId = $get('category_id');
                        if (!$roomId || !$categoryId) return [];

                        return Barang::where('room_id', $roomId)
                            ->where('category_id', $categoryId)
                            ->where('condition', 'baik')
                            ->pluck('item_name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->noSearchResultsMessage('Data tidak ditemukan'),

                Textarea::make('problem')
                    ->label('Kondisi / Deskripsi Masalah')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('evaluasi')
                    ->label('Evaluasi')
                    ->rows(3)
                    ->columnSpanFull()
                    ->hidden(fn() => \Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'staff'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'our_purchasing' => 'Our Purchasing',
                        'waiting_material' => 'Waiting Material',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                    ])
                    ->default('pending')
                    ->required()
                    ->disabled(fn() => \Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()?->role === 'staff'),

                Forms\Components\FileUpload::make('condition_pict_path')
                    ->label('Gambar Kondisi')
                    ->disk('public')
                    ->directory('condition_pictures')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label('No'),

                TextColumn::make('barang.room.room_name')->label('Ruangan')->searchable(),
                TextColumn::make('barang.category.category_name')->label('Kategori')->searchable(),
                TextColumn::make('barang.item_name')->label('Barang')->searchable(),
                TextColumn::make('barang.type')->label('Type')->sortable()->searchable(),
                TextColumn::make('problem')->label('Problem Req')->searchable(),
                TextColumn::make('user.name')->label('Nama Staff')->searchable(),
                TextColumn::make('evaluasi')->label('Evaluasi')->hidden()->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray' => fn($state) => is_null($state),
                        'warning' => 'our_purchasing',
                        'info' => 'waiting_material',
                        'primary' => 'in_progress',
                        'success' => 'done',
                    ])
                    ->formatStateUsing(function (?string $state) {
                        return match ($state) {
                            'pending' => 'Pending',
                            'our_purchasing' => 'Our Purchasing',
                            'waiting_material' => 'Waiting Material',
                            'in_progress' => 'In Progress',
                            'done' => 'Done',
                            default => '-',
                        };
                    })
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Upload')
                    ->date('Y-m-d')
                    ->sortable(),

                // ImageColumn::make('condition_pict_path')
                //     ->label('Gambar')
                //     ->disk('public')
                //     ->visibility('visible')
                //     ->height(100)
                //     ->width(100)
                //     ->hidden(), // Tetap hidden kecuali Anda ingin menampilkannya
            ])
            ->filters([
                // Filter tanggal upload
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Maintenance Request')
                    ->modalSubmitAction(false)
                    ->modalContent(
                        fn($record) => view('filament.resources.maintenance-req-resource.pages.view-maintenance-req', compact('record'))
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenanceReqs::route('/'),
            'create' => Pages\CreateMaintenanceReq::route('/create'),
        ];
    }
}

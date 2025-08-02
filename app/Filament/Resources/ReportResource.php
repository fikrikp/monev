<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\MaintenanceReq;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReportResource extends Resource
{
    protected static ?string $model = MaintenanceReq::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'reports';
    protected static ?string $modelLabel = 'Report';
    protected static ?string $pluralModelLabel = 'Reports';

    // --- TAMBAHKAN METODE INI KEMBALI ---
    public static function canViewAny(): bool
    {
        return Auth::user()?->role === 'admin' || Auth::user()?->role === 'chief_engineering';
    }
    // ------------------------------------

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'done');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')->rowIndex()->label('No'),
                Tables\Columns\TextColumn::make('barang.room.room_name')->label('Ruangan')->searchable(),
                Tables\Columns\TextColumn::make('barang.category.category_name')->label('Kategori')->searchable(),
                Tables\Columns\TextColumn::make('barang.item_name')->label('Barang')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Nama Staff')->searchable(),
                Tables\Columns\TextColumn::make('evaluasi')->label('Evaluasi')->hidden()->searchable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Tanggal Selesai')->date('Y-m-d'),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () use ($table) {
                        $activeFilters = $table->getFiltersForm()->getState();

                        $startDate = $activeFilters['Tanggal Upload']['start_date'] ?? null;
                        $endDate = $activeFilters['Tanggal Upload']['end_date'] ?? null;

                        $url = route('maintenance.export', array_filter([
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                        ]));

                        return redirect($url);
                    }),
            ])
            ->filters([
                Filter::make('Tanggal Upload')
                    ->form([
                        DatePicker::make('start_date')->label('Dari Tanggal'),
                        DatePicker::make('end_date')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn($q) => $q->whereDate('created_at', '>=', $data['start_date'])
                            )
                            ->when(
                                $data['end_date'],
                                fn($q) => $q->whereDate('created_at', '<=', $data['end_date'])
                            );
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
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReports::route('/'),
        ];
    }
}

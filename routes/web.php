<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaintenanceReqController;
use App\Exports\MaintenanceReqExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;


Route::get('/maintenance/export', function (Request $request) {
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');

    return Excel::download(new MaintenanceReqExport($startDate, $endDate), 'maintenance_reports.xlsx');
})->name('maintenance.export');


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', fn() => redirect('/admin/login'));

Route::middleware(['auth'])->prefix('evaluasi')->group(function () {

    Route::post('/maintenance/{maintenanceReq}/add-evaluation', [MaintenanceReqController::class, 'addEvaluation'])
        ->name('maintenance.add-evaluation');

    Route::put('/ubah-status/{maintenanceReq}', [MaintenanceReqController::class, 'ubahStatus'])
        ->name('evaluasi.ubah-status');

    Route::put('/update-gambar/{maintenanceReq}', [MaintenanceReqController::class, 'updateGambar'])
        ->name('evaluasi.update-gambar');
});

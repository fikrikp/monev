<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MaintenanceReqController extends Controller
{    // Fungsi-fungsi lain di sini tidak diubah
    public function ubahStatus(Request $request, MaintenanceReq $maintenanceReq)
    {
        Log::info('Status Request Data:', $request->all());

        // Otorisasi: Pastikan Supervisor yang bisa
        if (Auth::user()->role !== 'supervisor') {
            Notification::make()
                ->title('Akses Ditolak!')
                ->body('Anda tidak memiliki izin untuk mengubah status.')
                ->danger()
                ->send();
            return back();
        }

        try {
            $validatedData = $request->validate([
                'status' => 'required|in:our_purchasing,waiting_material,in_progress,done'
            ]);

            $updated = $maintenanceReq->update(['status' => $validatedData['status']]);

            if ($updated && $maintenanceReq->status === 'done') {
                $maintenanceReq->barang->update(['condition' => 'baik']);
            }

            if ($updated) {
                Notification::make()
                    ->title('Status Berhasil Diubah!')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Gagal Mengubah Status.')
                    ->body('Terjadi masalah saat memperbarui record status.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Log::error('Error update status: ' . $e->getMessage());
            Notification::make()
                ->title('Terjadi Kesalahan!')
                ->body('Terjadi kesalahan saat mencoba mengubah status.')
                ->danger()
                ->send();
        }
        return back();
    }

    public function updateGambar(Request $request, MaintenanceReq $maintenanceReq)
    {
        // Otorisasi: Pastikan Supervisor yang bisa
        if (Auth::user()->role !== 'supervisor') {
            Notification::make()->title('Aksi tidak diizinkan!')->danger()->send();
            return back();
        }

        $request->validate(['condition_pict' => 'required|image|max:2048']);

        try {
            if ($maintenanceReq->condition_pict_path) {
                Storage::disk('public')->delete($maintenanceReq->condition_pict_path);
            }

            $path = $request->file('condition_pict')->store('condition_pictures', 'public');
            $updated = $maintenanceReq->update(['condition_pict_path' => $path]);

            if ($updated) {
                Notification::make()->title('Gambar kondisi berhasil diperbarui.')->success()->send();
            } else {
                Notification::make()->title('Gagal memperbarui gambar.')->danger()->send();
            }
        } catch (\Exception $e) {
            Log::error('Error update gambar: ' . $e->getMessage());
            Notification::make()->title('Terjadi Kesalahan!')->body('Terjadi kesalahan saat mencoba memperbarui gambar.')->danger()->send();
        }
        return back();
    }

    public function addEvaluation(Request $request, MaintenanceReq $maintenanceReq)
    {
        if (Auth::user()->role !== 'chief_engineering') {
            Notification::make()
                ->title('Akses Ditolak!')
                ->body('Anda tidak memiliki izin untuk menambahkan evaluasi.')
                ->danger()
                ->send();
            return back();
        }

        $request->validate([
            'evaluasi' => 'required|string|max:1000',
        ]);

        try {
            $maintenanceReq->evaluasi = $request->evaluasi;
            $maintenanceReq->save();

            Notification::make()
                ->title('Evaluasi berhasil ditambahkan.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error('Error menambahkan evaluasi: ' . $e->getMessage());
            Notification::make()
                ->title('Terjadi Kesalahan!')
                ->body('Gagal menambahkan evaluasi.')
                ->danger()
                ->send();
        }

        return redirect()->route('filament.admin.resources.maintenance-reqs.index');
    }
}

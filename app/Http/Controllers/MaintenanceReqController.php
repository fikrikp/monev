<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MaintenanceReqController extends Controller
{

    public function kirimEvaluasi(Request $request, MaintenanceReq $maintenanceReq)
    {
        Log::info('MASUK ke kirimEvaluasi()', [
            'user_id' => Auth::user()->id,
            'role' => Auth::user()?->role,
            'evaluasi' => $request->evaluasi,
        ]);
        if (Auth::check() && Auth::user()->role !== 'chief_engineering') {
            if (Auth::check() && Auth::user()->role !== 'chief_engineering') {
                Notification::make()
                    ->title('Akses Ditolak!')
                    ->body('Anda tidak memiliki izin untuk mengirim evaluasi.')
                    ->danger()
                    ->send();
                return back();

                try {
                    $validatedData = $request->all();

                    $updated = $maintenanceReq->update(['evaluasi' => $validatedData['evaluasi']]);

                    if ($updated) {
                        Notification::make()
                            ->title('Evaluasi Berhasil Dikirim!')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Gagal Menyimpan Evaluasi.')
                            ->body('Terjadi masalah saat memperbarui record evaluasi.')
                            ->danger()
                            ->send();
                    }
                } catch (\Exception $e) {
                    Log::error('Error update evaluasi: ' . $e->getMessage());
                    Notification::make()
                        ->title('Terjadi Kesalahan!')
                        ->body('Terjadi kesalahan saat mencoba menyimpan evaluasi.')
                        ->danger()
                        ->send();
                }
                return back();
            }
        }
    }

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

            // ✅ PERBAIKAN PENTING DI SINI: Perbarui kondisi Barang jika statusnya 'done'
            if ($updated && $maintenanceReq->status === 'done') {
                $maintenanceReq->barang->update(['condition' => 'baik']); // Pastikan kolom 'condition' di model Barang ada di $fillable

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceReq extends Model
{
    use HasFactory;

    protected $table = 'maintenance_req';

    protected $fillable = [
        'id_barang',
        'nama_staff',
        'problem',
        'evaluasi',
        'status',
        'condition_pict_path',
        'user_id',
    ];


    public function user(): BelongsTo // Tambahkan tipe hint BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function barang(): BelongsTo // Tambahkan tipe hint BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}

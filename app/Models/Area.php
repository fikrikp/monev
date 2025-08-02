<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Area extends Model
{
    use HasFactory;

    protected $table = 'area';

    protected $fillable = [
        'area_name',
    ];

    /**
     * Get the rooms for the area.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'id_area');
    }
}

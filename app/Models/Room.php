<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Nama tabel jika berbeda dari konvensi jamak 'rooms'
    protected $table = 'rooms';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'conference_id',
        'room_name'
    ];

    // Definisi relasi 'belongsTo' ke Conference
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
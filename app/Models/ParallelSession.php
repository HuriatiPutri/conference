<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParallelSession extends Model
{
    use HasFactory;

    // Nama tabel jika berbeda dari konvensi jamak 'rooms'
    protected $table = 'parallel_sessions';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'audience_id',
        'room_id',
        'name_of_presenter',
        'paper_title',
    ];

    // Definisi relasi 'belongsTo' ke Conference
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function audience()
    {
        return $this->belongsTo(Audience::class);
    }
}

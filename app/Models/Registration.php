<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    // Nama tabel jika berbeda dari konvensi jamak 'registrations'
    protected $table = 'registrations';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'conference_id',
        'first_name',
        'last_name',
        'paper_title',
        'institution',
        'email',
        'phone_number',
        'country',
        'presentation_type',
        'paid_fee',
        'full_paper_path',
    ];

    // Definisi relasi 'belongsTo' ke Conference
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
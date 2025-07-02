<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audience extends Model
{
    use HasFactory;

    protected $table = 'audiences';

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
        'payment_status', // Tambahkan ini
        'payment_method', // Tambahkan ini
        'payment_proof_path', // Tambahkan ini
        'full_paper_path',
    ];

    // Definisikan 'payment_status' sebagai enum di PHP
    protected $casts = [
        'payment_status' => 'string', // Atau bisa juga array untuk enum jika PHP >= 8.1
        'payment_method' => 'string',
    ];

    // Relasi 'belongsTo' ke Conference
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
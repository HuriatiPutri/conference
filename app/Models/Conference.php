<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'conferences'; // Secara default Laravel akan mencari 'conferences', tapi eksplisit lebih baik

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'public_id',
        'name',
        'initial',
        'cover_poster_path',
        'date',
        'city',
        'country',
        'year',
        'online_fee',
        'onsite_fee',
        'participant_fee',
        'online_fee_usd',
        'onsite_fee_usd',
        'participant_fee_usd',
    ];

    /**
     * The attributes that should be cast.
     * Untuk memastikan 'date' adalah objek Carbon.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'online_fee' => 'decimal:2',
        'onsite_fee' => 'decimal:2',
        'participant_fee' => 'decimal:2',
        'online_fee_usd' => 'decimal:2',
        'onsite_fee_usd' => 'decimal:2',
        'participant_fee_usd' => 'decimal:2',
    ];

    // Jika ada relasi dengan tabel registrations, bisa didefinisikan di sini
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}

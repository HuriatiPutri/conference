<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceHistory extends Model
{
    use HasFactory;
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_history'; // Secara default Laravel akan mencari 'conferences', tapi eksplisit lebih baik

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'audience_id',
        'snap_token',
        'expired_at', // Tambahkan ini untuk menyimpan waktu kedaluwarsa token
        'redirect_url', // Tambahkan ini untuk menyimpan URL redirect
        'payment_method', // Tambahkan ini untuk menyimpan metode pembayaran
        'amount',
        'status',
    ];


     // Jika ada relasi dengan tabel registrations, bisa didefinisikan di sini
     public function registrations()
     {
         return $this->hasMany(Audience::class);
     }

}

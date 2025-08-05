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

    public function getPaymentMethodText()
    {
        switch ($this->payment_method) {
            case 'transfer_bank':
                return 'Transfer Bank';
            case 'payment_gateway':
                return 'Payment Gateway';
            default:
                return 'Metode Pembayaran Tidak Diketahui';
        }
    }

    public function getPresentationTypeText()
    {
        switch ($this->presentation_type) {
            case 'online_author':
                return 'Online (author/presenter)';
            case 'onsite':
                return 'Onsite';
            case 'participant_only':
                return 'Participant Only';
            default:
                return 'Jenis Peserta Tidak Diketahui';
        }
    }


    public function getPaymentStatusText()
    {
        switch ($this->payment_status) {
            case 'pending_payment':
                return '<span class="badge badge-warning">Menunggu Pembayaran</span>';
            case 'paid':
                return '<span class="badge badge-success">Sudah Dibayar</span>';
            case 'failed':
                return '<span class="badge badge-danger">Pembayaran Gagal</span>';
            case 'refunded':
                return '<span class="badge badge-info">Dikembalikan</span>';
            case 'cancelled':
                return '<span class="badge badge-danger">Dibatalkan</span>';
            default:
                return '<span class="badge badge-dark">Status Tidak Diketahui</span>';
        }
    }

    // Relasi 'belongsTo' ke Conference
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
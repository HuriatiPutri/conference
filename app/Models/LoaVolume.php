<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoaVolume extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'loa_volume';

    protected $fillable = [
        'volume',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'deleted_at'
    ];

    /**
     * Get the user who created this LoA Volume
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this LoA Volume
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all audiences that use this LoA Volume
     */
    public function audiences()
    {
        return $this->hasMany(Audience::class, 'loa_volume_id');
    }
}

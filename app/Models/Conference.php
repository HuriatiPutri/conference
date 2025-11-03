<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conference extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'public_id',
        'name',
        'description',
        'initial',
        'cover_poster_path',
        'certificate_template_path',
        'certificate_template_position',
        'date',
        'registration_start_date',
        'registration_end_date',
        'year',
        'city',
        'country',
        'online_fee',
        'online_fee_usd',
        'onsite_fee',
        'onsite_fee_usd',
        'participant_fee',
        'participant_fee_usd',
    ];
    
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function audiences()
    {
        return $this->hasMany(Audience::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}

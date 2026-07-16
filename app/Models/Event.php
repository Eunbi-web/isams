<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'type', 'description',
        'event_date', 'end_date',
        'time_start', 'time_end',
        'venue', 'organizer',
        'max_participants', 'actual_participants',
        'status', 'banner',
    ];

    protected $casts = [
        'event_date' => 'date',
        'end_date'   => 'date',
    ];

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now())->where('status', 'Upcoming');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'Ongoing');
    }
}

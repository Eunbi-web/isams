<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'acronym', 'type', 'description',
        'adviser', 'president',
        'year_founded', 'status',
        'logo',
    ];

    public function members()
    {
        return $this->belongsToMany(Student::class, 'organization_members')
                    ->withPivot(['role', 'joined_at'])
                    ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplineCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_number', 'student_id',
        'violation_type', 'incident_date',
        'description', 'witnesses',
        'status', 'hearing_date',
        'sanction', 'remarks',
        'filed_by',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'hearing_date'  => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['Resolved', 'Dismissed']);
    }
}

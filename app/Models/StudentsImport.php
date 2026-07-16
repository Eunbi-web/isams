<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class StudentsImport extends Model
{


    protected $table = 'students_imports';

    protected $fillable = [
        'import_batch_id',
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
    ];


    protected $casts = [
        'birthdate' => 'date',
        'gwa' => 'decimal:2',
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ?? '') . ' ' . $this->last_name);
    }
}


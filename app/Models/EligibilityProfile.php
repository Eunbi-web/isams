<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EligibilityProfile extends Model {
    protected $table = 'eligibility_profiles';

    protected $fillable = [
        'student_id','gwa','year_level','enrollment_type',
        'income_bracket','academic_honors','has_failing','has_discipline',
        'school_id_photo','coe_file','family_income',
    ];

    protected $casts = [
        'has_failing'    => 'boolean',
        'has_discipline' => 'boolean',
        'gwa'            => 'decimal:2',
        'family_income'  => 'decimal:2',
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }
}

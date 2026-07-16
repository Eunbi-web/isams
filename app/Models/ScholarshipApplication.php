<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ScholarshipApplication extends Model {
    use HasFactory;
    protected $fillable = ['student_id','scholarship_id','gwa','enrollment_type','has_failing','has_discipline','income_bracket','essay','remarks','docs_submitted','status','ai_score','ai_eligibility','ai_tag','ai_reasoning','ai_run_at'];
    protected $casts    = ['has_failing'=>'boolean','has_discipline'=>'boolean','gwa'=>'decimal:2','ai_score'=>'integer','ai_run_at'=>'datetime','docs_submitted'=>'array'];
    public function student()    { return $this->belongsTo(Student::class); }
    public function scholarship(){ return $this->belongsTo(Scholarship::class); }
    public function scopeEligible($q)  { return $q->where('ai_eligibility','Eligible'); }
    public function scopeForReview($q) { return $q->where('ai_eligibility','For Review'); }
    public function scopeNotEligible($q){ return $q->where('ai_eligibility','Not Eligible'); }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Student extends Model {
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id','first_name','middle_name','last_name','student_id','course','year_level','section','academic_year','semester','enrollment_type','gwa','status','birthdate','sex','civil_status','contact_number','email','address','photo','guardian_name','guardian_relationship','guardian_contact'];
    protected $casts    = ['birthdate'=>'date','gwa'=>'decimal:2'];
    public function getFullNameAttribute(): string { return trim("{$this->first_name} {$this->middle_name} {$this->last_name}"); }
    public function user()         { return $this->belongsTo(User::class); }
    public function applications() { return $this->hasMany(ScholarshipApplication::class); }
    public function scholarships() { return $this->belongsToMany(Scholarship::class,'scholarship_grantees')->withPivot(['status','gwa_at_award','awarded_at','remarks'])->withTimestamps(); }
}

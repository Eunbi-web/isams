<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Scholarship extends Model {
    use HasFactory;
    protected $fillable = ['name','type','description','benefits','requirements','slots','amount','source','start_date','end_date','status','ai_criteria'];
    protected $casts    = ['start_date'=>'date','end_date'=>'date','amount'=>'decimal:2','ai_criteria'=>'array'];
    public function applications() { return $this->hasMany(ScholarshipApplication::class); }
    public function grantees()     { return $this->belongsToMany(Student::class,'scholarship_grantees')->withPivot(['status','gwa_at_award','awarded_at','remarks'])->withTimestamps(); }
    public function getSlotsRemainingAttribute(): int { return max(0, ($this->slots ?? 0) - $this->applications()->where('status','Approved')->count()); }
}

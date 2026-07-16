<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CounselingSession extends Model {
    use HasFactory;
    protected $fillable = ['student_id','counselor_id','concern_type','concern_detail','priority','preferred_time','preferred_date','session_date','session_time','venue','queue_position','status','notes'];
    protected $casts    = ['preferred_date'=>'date','session_date'=>'date'];
    public function student()  { return $this->belongsTo(Student::class); }
    public function counselor(){ return $this->belongsTo(User::class,'counselor_id'); }
}

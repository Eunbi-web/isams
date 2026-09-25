<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AdminMessage extends Model {
    use HasFactory;
    protected $fillable = ['sent_by','student_id','subject','body','is_read','read_at'];
    protected $casts    = ['is_read'=>'boolean','read_at'=>'datetime'];
    public function student() { return $this->belongsTo(Student::class); }
}

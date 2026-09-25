<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Complaint extends Model {
    use HasFactory;
    protected $fillable = ['student_id','user_id','type','subject','description','is_anonymous','status','admin_reply','replied_at','replied_by'];
    protected $casts    = ['is_anonymous'=>'boolean','replied_at'=>'datetime'];
    public function student() { return $this->belongsTo(Student::class); }
    public function user()    { return $this->belongsTo(User::class); }
}

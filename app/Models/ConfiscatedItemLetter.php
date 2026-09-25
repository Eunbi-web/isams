<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ConfiscatedItemLetter extends Model {
    use HasFactory;
    protected $fillable = ['student_id','user_id','item_description','date_confiscated','confiscated_by','reason_confiscated','letter_content','status','admin_notes','reviewed_at','reviewed_by'];
    protected $casts    = ['date_confiscated'=>'date','reviewed_at'=>'datetime'];
    public function student() { return $this->belongsTo(Student::class); }
}

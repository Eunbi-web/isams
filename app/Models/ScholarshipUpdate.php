<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ScholarshipUpdate extends Model {
    use HasFactory;
    protected $fillable = ['scholarship_id','user_id','title','body','source_type'];
    public function scholarship() { return $this->belongsTo(Scholarship::class); }
    public function postedBy()    { return $this->belongsTo(User::class,'user_id'); }
}

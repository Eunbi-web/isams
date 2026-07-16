<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Announcement extends Model {
    use HasFactory;

    // DB schema uses announcements_all (not announcements)
    protected $table = 'announcements_all';

    protected $fillable = ['title','body','type','priority','user_id','scholarship_id','published_at','expires_at'];
    protected $casts    = ['published_at'=>'datetime','expires_at'=>'datetime'];
    public function author()     { return $this->belongsTo(User::class,'user_id'); }
    public function scholarship(){ return $this->belongsTo(Scholarship::class); }
}


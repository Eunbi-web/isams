<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LoginLog extends Model {
    protected $fillable = ['user_id','email','ip_address','user_agent','status','role','logged_in_at','logged_out_at'];
    protected $casts    = ['logged_in_at'=>'datetime','logged_out_at'=>'datetime'];
    public function user() { return $this->belongsTo(User::class); }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
    use HasFactory, Notifiable;
    protected $table = 'users_all';
    protected $fillable = ['name','email','password','role','department','theme','contact_number','avatar','is_active','last_login_at','last_login_ip'];
    protected $hidden   = ['password','remember_token'];
    protected function casts(): array { return ['email_verified_at'=>'datetime','password'=>'hashed','last_login_at'=>'datetime','is_active'=>'boolean']; }
    public function isSuperAdmin(): bool { return $this->role === 'superadmin'; }
    public function isAdmin(): bool      { return in_array($this->role,['superadmin','admin','officer']); }
    public function isStudent(): bool    { return $this->role === 'student'; }
    public function student()    { return $this->hasOne(Student::class); }
    public function loginLogs()  { return $this->hasMany(LoginLog::class); }
}

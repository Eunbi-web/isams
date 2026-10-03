<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Scholar extends Model {
    use HasFactory;
    protected $fillable = [
        'student_number','first_name','middle_name','last_name','course','year_level',
        'scholarship_name','scholarship_type','status',
        'current_gwa','enrollment_status','requirements_met','graduation_status',
        'remarks','source','last_monitored_at',
    ];
    protected $casts = ['requirements_met'=>'boolean','last_monitored_at'=>'datetime'];
    public function getFullNameAttribute(): string {
        return trim(($this->first_name ?? '').' '.($this->middle_name ? $this->middle_name.' ' : '').($this->last_name ?? ''));
    }
    /** True while the scholar is an active, enrolled grantee. */
    public function getIsMaintainingAttribute(): bool {
        return $this->status === 'Active'
            && $this->enrollment_status === 'Enrolled'
            && $this->requirements_met;
    }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DisciplineRecord extends Model {
    protected $table    = 'discipline_records';
    protected $fillable = ['edp_number','student_name','department','offense_category','description','incident_date','guardian_name','guardian_contact','status','created_by'];
    protected $casts    = ['incident_date'=>'date','created_by'=>'integer'];
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ScrapedScholarship extends Model {
    use HasFactory;
    protected $fillable = [
        'name','benefits','requirements','deadline','slots','is_open',
        'source_url','source_agency','source_type',
        'status','ai_confidence','imported','scholarship_id','last_scraped_at',
    ];
    protected $casts = [
        'is_open'=>'boolean','imported'=>'boolean',
        'ai_confidence'=>'integer','slots'=>'integer',
        'deadline'=>'date','last_scraped_at'=>'datetime',
    ];
    public function scholarship() { return $this->belongsTo(Scholarship::class); }
    public function scopeNew($q)      { return $q->where('status','new'); }
    public function scopeUpdated($q)  { return $q->where('status','updated'); }
    public function scopeNotImported($q){ return $q->where('imported',false); }
}

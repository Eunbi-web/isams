<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AiReport extends Model {
    use HasFactory;
    protected $fillable = ['portal','scope','title','academic_year','semester','prepared_by','content_html'];
}

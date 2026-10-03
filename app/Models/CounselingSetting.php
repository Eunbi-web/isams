<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CounselingSetting extends Model {
    use HasFactory;
    protected $fillable = ['counselor_id','scheduling_mode','slot_capacity'];

    /** Single-row global scheduling configuration; created on first use. */
    public static function current(): self {
        return static::query()->firstOrCreate([], ['scheduling_mode'=>'queue','slot_capacity'=>2]);
    }

    public static function schedulingMode(): string {
        return static::current()->scheduling_mode;
    }

    /** Counseling request time slots available for booking. */
    public const SLOTS = [
        '8:00 AM – 9:00 AM',
        '9:00 AM – 10:00 AM',
        '10:00 AM – 11:00 AM',
        '1:00 PM – 2:00 PM',
        '2:00 PM – 3:00 PM',
        '3:00 PM – 4:00 PM',
    ];
}

<?php

namespace App\Models;

use App\Models\Scopes\SortingScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDate extends Model
{
   use HasFactory ;

    protected $guarded = [];
    protected $appends = [];
    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d',
    ];

     protected static function booted(): void
    {
        static::addGlobalScope(new SortingScope);
    }

       public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class,'booking_dates_id');
    }

    public function addonService()
{
    return $this->belongsTo(AddonService::class);
}

}

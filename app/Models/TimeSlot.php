<?php

namespace App\Models;

use App\Models\Scopes\SortingScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
   use HasFactory ;
    protected $table = 'time_slotes';
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

   public function bookingDate()
    {
        return $this->belongsTo(BookingDate::class);
    }
}

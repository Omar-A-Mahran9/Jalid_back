<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
 use App\Models\BookingDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingDateController extends Controller
{
public function index(Request $request)
{
    $this->authorize('view_booking_dates');

    if ($request->ajax()) {
        return response(getModelData(model: new BookingDate(), relations: [
             'timeSlots' => ['id', 'booking_dates_id', 'time'] // include foreign key for proper eager loading
        ]));
    } else {
         return view('dashboard.booking_dates.index' );
    }
}







public function store(Request $request)
{
    $schedules = $request->input('schedules'); // weekly template
    $startDate = Carbon::now()->startOfDay();
    $endDate = Carbon::now()->addMonths(2)->endOfDay();

    // Loop over each date in the 2-month range
    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        $dayName = $date->format('l'); // e.g., "Monday", "Tuesday"

        if (!isset($schedules[$dayName])) {
            continue;
        }

        $dayConfig = $schedules[$dayName];

        $bookingDate = BookingDate::updateOrCreate(
            ['day_date' => $date->toDateString()],
            ['is_available' => (bool) $dayConfig['is_available']]
        );

        // Delete old slots to prevent duplication
        $bookingDate->timeSlots()->delete();

        // Create time slots
        foreach ($dayConfig['times'] as $slot) {
            if (!empty($slot['time'])) {
                $bookingDate->timeSlots()->create([
                    'time' => $slot['time']
                ]);
            }
        }
    }
 
 }







public function update(Request $request, BookingDate $bookingDate)
{
     $validated = $request->validate([
        'day_date' => 'required|date|after_or_equal:today',
         'time_slots' => 'required|array|min:1',
        'time_slots.*.time' => 'required|date_format:H:i',
    ]);

    $exists = BookingDate::where('day_date', $validated['day_date'])
        ->where('id', '!=', $bookingDate->id)
        ->exists();

    if ($exists) {
        return response()->json([
            'errors' => [
                'day_date' => [__('Duplicate date for selected date.')],
            ]
        ], 422);
    }

    try {
        // Step 1: Update booking date
        $bookingDate->update([
            'day_date' => $validated['day_date'],
        ]);

        // Step 2: Delete old time slots
        $bookingDate->timeSlots()->delete();

        // Step 3: Save new time slots
        foreach ($validated['time_slots'] as $slot) {
            $bookingDate->timeSlots()->create([
                'time' => $slot['time'],
            ]);
        }

        return response()->json(['message' => 'Booking date and time slots updated successfully.']);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'Failed to update booking data.',
            'error' => $e->getMessage()
        ], 500);
    }
}

 public function destroy(BookingDate $bookingDate)
{

        // Delete the booking date and its related time slots (if cascade not set in DB)
        $bookingDate->timeSlots()->delete(); // optional if cascade delete is set in DB
        $bookingDate->delete();

        return response()->json(['message' => 'Booking date deleted successfully.']);

}

}

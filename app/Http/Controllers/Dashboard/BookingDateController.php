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
    dd($request);
    // Validate input (you can expand this as needed)
    $validated = $request->validate([
        'schedules' => 'required|array',
        'schedules.*.is_available' => 'required|boolean',
        'schedules.*.times' => 'array',
        'schedules.*.times.*.time' => 'required_if:schedules.*.is_available,1|date_format:H:i',
    ]);

    $schedules = $validated['schedules'];

    $startDate = Carbon::today();
    $endDate = $startDate->copy()->addMonths(2);

    // Loop through each date in the next 2 months
    for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
        $dayName = $date->format('l'); // e.g., Saturday

        if (!isset($schedules[$dayName])) {
            continue;
        }

        $daySchedule = $schedules[$dayName];

        $isAvailable = (bool) $daySchedule['is_available'];
        $times = $daySchedule['times'] ?? [];

        // If marked available but no times, force unavailable
        if ($isAvailable && count($times) === 0) {
            $isAvailable = false;
        }

        // Find existing or create new BookingDate record
        $bookingDate = BookingDate::firstOrNew(['day_date' => $date->toDateString()]);

        $bookingDate->is_available = $isAvailable;
        $bookingDate->save();

        // Delete old time slots if any
        $bookingDate->timeSlots()->delete();

        // Insert new time slots only if available
        if ($isAvailable) {
            $timeSlotsData = [];
            foreach ($times as $timeItem) {
                if (!empty($timeItem['time'])) {
                    $timeSlotsData[] = [
                        'time' => $timeItem['time'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            if (!empty($timeSlotsData)) {
                $bookingDate->timeSlots()->createMany($timeSlotsData);
            }
        }
    }

    return redirect()->back()->with('success', 'Schedule saved successfully.');
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

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

    // Define range consistent with store method (today to 2 months from now)
    $startDate = Carbon::now()->startOfDay();
    $endDate = Carbon::now()->addMonths(2)->endOfDay();

    // Get all BookingDates in the range with timeSlots eager loaded
    $bookingDates = BookingDate::with('timeSlots')
        ->whereBetween('day_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->get();

    // Initialize schedules array with all days of the week having default values
    $schedules = [];
    foreach (['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day) {
        $schedules[$day] = [
            'is_available' => false,
            'times' => [],
        ];
    }

    // Aggregate data from bookingDates
    // Note: There can be multiple BookingDate records per day name in the range (e.g. multiple Mondays),
    // but your form works with weekly template, so let's merge them:
    // - If any BookingDate for that weekday is available, mark available
    // - Collect all distinct time slots for that weekday

    $timesPerDay = []; // temporary array to collect all times per weekday

    foreach ($bookingDates as $bookingDate) {
        $dayName = Carbon::parse($bookingDate->day_date)->format('l');

        if (!isset($timesPerDay[$dayName])) {
            $timesPerDay[$dayName] = [];
        }

        if ($bookingDate->is_available) {
            $schedules[$dayName]['is_available'] = true;
        }

        // Collect times (unique)
        foreach ($bookingDate->timeSlots as $slot) {
            $time = $slot->time;
            if (!in_array($time, $timesPerDay[$dayName])) {
                $timesPerDay[$dayName][] = $time;
            }
        }
    }

    // Assign collected unique times to schedules array
    foreach ($timesPerDay as $day => $times) {
        $schedules[$day]['times'] = array_map(fn($time) => ['time' => $time], $times);
    }

    if ($request->ajax()) {
        // Return JSON for AJAX requests
        return response()->json($schedules);
    }

    // For normal requests, pass schedules to view
    return view('dashboard.booking_dates.index', compact('schedules'));
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

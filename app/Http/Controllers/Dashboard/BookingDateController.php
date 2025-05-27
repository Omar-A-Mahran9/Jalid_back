<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AddonService;
use App\Models\BookingDate;
use Illuminate\Http\Request;

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
    $validated = $request->validate([
        'day_date' => 'required|date|after_or_equal:today',
        'time_slots' => 'required|array|min:1',
        'time_slots.*.time' => 'required|date_format:H:i',
    ]);
    $exists = BookingDate::where('day_date', $validated['day_date'])->exists();

    if ($exists) {
        return response()->json([
            'errors' => [
                'day_date' => [__('Duplicate date for selected Date.')],
            ]
        ], 422);
    }
     try {
        // Step 1: Create booking date
        $bookingDate = BookingDate::create([
            'day_date' => $validated['day_date'],

        ]);

        // Step 2: Save time slots
        foreach ($validated['time_slots'] as $slot) {
            $bookingDate->timeSlots()->create([
                'time' => $slot['time'],
            ]);
        }


        // Return JSON success for AJAX
        return response()->json(['message' => 'Booking date and time slots saved successfully.']);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'Failed to save booking data.',
            'error' => $e->getMessage()
        ], 500);
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

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
            'addonService' => ['id', 'name_ar', 'name_en', 'description_ar', 'description_en'],
            'timeSlots' => ['id', 'booking_dates_id', 'time'] // include foreign key for proper eager loading
        ]));
    } else {
        $addonServices = AddonService::get();
        return view('dashboard.booking_dates.index', compact('addonServices'));
    }
}



public function store(Request $request)
{
    $validated = $request->validate([
        'day_date' => 'required|date',
        'addon_service_id' => 'required|exists:addon_services,id', // or appropriate validation
        'time_slots' => 'required|array|min:1',
        'time_slots.*.time' => 'required|date_format:H:i',
    ]);
    // Check if same day_date + addon_service_id already exists
    $exists = BookingDate::where('day_date', $validated['day_date'])
        ->where('addon_service_id', $validated['addon_service_id'])
        ->exists();

    if ($exists) {
        return response()->json([
            'errors' => [
                'addon_service_id' => [__('This service already has a booking on the selected date.')],
                'day_date' => [__('Duplicate date for selected service.')],
            ]
        ], 422);
    }
     try {
        // Step 1: Create booking date
        $bookingDate = BookingDate::create([
            'day_date' => $validated['day_date'],
            'addon_service_id' => $validated['addon_service_id'],

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
        'day_date' => 'required|date',
        'addon_service_id' => 'required|exists:addon_services,id',
        'time_slots' => 'required|array|min:1',
        'time_slots.*.time' => 'required|date_format:H:i',
    ]);

    // Check if same day_date + addon_service_id already exists except current record
    $exists = BookingDate::where('day_date', $validated['day_date'])
        ->where('addon_service_id', $validated['addon_service_id'])
        ->where('id', '!=', $bookingDate->id)
        ->exists();

    if ($exists) {
        return response()->json([
            'errors' => [
                'addon_service_id' => [__('This service already has a booking on the selected date.')],
                'day_date' => [__('Duplicate date for selected service.')],
            ]
        ], 422);
    }

    try {
        // Step 1: Update booking date
        $bookingDate->update([
            'day_date' => $validated['day_date'],
            'addon_service_id' => $validated['addon_service_id'],
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

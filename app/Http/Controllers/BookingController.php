<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ServiceBooking;

class BookingController extends Controller
{
    public function changeStatus(Request $request)
    {
        $booking = ServiceBooking::findOrFail($request->id);
        $booking->status = $request->status;
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully.'
        ]);
    }
}
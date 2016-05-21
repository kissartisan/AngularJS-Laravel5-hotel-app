<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

// I added these
use App\Reservation;
use App\RoomCalendar;
use App\ReservationNight;
use App\Customer;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function createReservation(Request $request)
    {
    	// Get the room_info array of objects
    	$room_info = $request['room_info'];

    	// Get the start date & end date that came from the reservationData
    	$start_dt = Carbon::createFromFormat('d-m-Y', $request['start_dt'])->toDateString();
    	$end_dt = Carbon::createFromFormat('d-m-Y', $request['end_dt'])->toDateString();

    	// Create or update a customer info
    	$customer = Customer::firstOrCreate($request['customer']);

    	// Set the reservations table
    	$reservation = Reservation::create();

    	// Get the values of the reservation
    	$reservation->total_price = $room_info['total_price'];
    	$reservation->occupancy = $request['occupancy'];
    	$reservation->customer_id = $customer->id;
    	$reservation->checkin = $start_dt;
    	$reservation->checkout = $end_dt;
    	// Save the changes on the reservation table
    	$reservation->save();

    	// Set the $date variable to $start_dt
    	$date = $start_dt;

    	// Loop through start date and end date of user choice
    	while (strtotime($date) < strtotime($end_dt)) {
    		// Set the $room_calendar variable where the day is equal to the start date
    		// And the room_type_id column is equal the the $room_info object id
    		$room_calendar = RoomCalendar::where('day', '=', $date)
    			->where('room_type_id', '=', $room_info['id'])->first();

    		// Set the reservation_nights table
    		$night = ReservationNight::create();
    		// Set the day column equal to the start date
    		$night->day = $date;

    		$night->rate = $room_calendar->rate;
    		$night->room_type_id = $room_info['id'];
    		$night->reservation_id = $reservation->id;

    		// Update the availability column to minus one and
    		// reservation column to plus one
    		$room_calendar->availability--;
    		$room_calendar->reservations++;
    		// Save changes to the room_calendars table
    		$room_calendar->save();
    		$night->save();

    		$date = date("Y-m-d", strtotime("+1 day ", strtotime($date)));
    	}

    	// Create another object ($nights) to populate the day and rate
    	$nights = $reservation->nights;
    	$customer = $reservation->customer;

    	return $reservation;
    }
}

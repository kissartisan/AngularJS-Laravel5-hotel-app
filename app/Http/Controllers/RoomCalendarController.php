<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * I added these
 */
use App\RoomCalendar;
use App\RoomType;
use Carbon\Carbon;

class RoomCalendarController extends Controller
{
    public function setPriceInRangeForRoomType(Request $request)
    {
    	$room_type = $request['room_type'];
    	$price = $request['price'];
    	$start_dt = $request['start_dt'];
    	$end_dt = $request['end_dt'];
    	$date = date("Y-m-d", strtotime($start_dt));

    	$base_room = RoomType::find($room_type);

    	$i = 0;

    	while (strtotime($date) <= strtotime($end_dt)) {
    		$room_day = RoomCalendar::firstOrNew(array('room_type_id' => $room_type, 'day' => $date));

    		if (!$room_day->id) {
    			$room_day->availability = $base_room->base_availability;
    		}

    		if (!isset($price)) {
    			$room_day->rate = $base_room->base_price;
    		} else {
    			$room_day->rate = $price;
    		}

    		$room_day->save();
    		$date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
    		$i++;
    	}


        $room_short_name = RoomType::where('id', $room_day->room_type_id)->pluck('short_name')->first();

    	return response("Success! updated " . $i . ' ' . strtolower($room_short_name) . " room dates", 200);
    }

    public function searchAvailability(Request $request)
    {
    	// Format the inputted dates
    	$start_dt = Carbon::createFromFormat('d-m-Y', $request['start_dt'])->toDateTimeString();
    	$end_dt = Carbon::createFromFormat('d-m-Y', $request['end_dt'])->toDateTimeString();

    	// Get the user selected occupancy
    	$min_occupancy = $request['min_occupancy'];

    	// If it is not set populate all the rooms
    	if (!isset($min_occupancy)) {
    		$min_occupancy = RoomType::min('max_occupancy');
    	}

    	// If set, populate the rooms where max_occupancy is greater than minimum occupancy
    	$room_types = RoomType::where('max_occupancy', '>=', $min_occupancy)->get();

    	$available_room_types = array();

    	// Loop through each room
    	foreach ($room_types as $room_type) {
    		$count = RoomCalendar::where('day', '>=', $start_dt)
    			->where('day','<', $end_dt)
    			->where('room_type_id', '=', $room_type->id)
    			->where('availability', '<=', 0)->count();

    		// Populate avalable rooms
    		if ($count == 0) {
    			$total_price = RoomCalendar::where('day', '>=', $start_dt)
    				->where('day', '<', $end_dt)
    				->where('room_type_id', '=', $room_type->id)
    				->sum('rate');

    			// Create a new object called total price and
    			// set its value equal to variable $total_price
    			$room_type->total_price = $total_price;

    			// Send the room type in the front end
    			array_push($available_room_types, $room_type);
    		}
    	}

    	return $available_room_types;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getStates()
    {
        return response()->json(State::orderBy('name')->get());
    }

    public function getCities(State $state)
    {
        return response()->json($state->cities()->orderBy('name')->get());
    }

    public function getLocationByPincode($pincode)
    {
        $city = City::with('state')->where('pincode', $pincode)->first();

        if ($city) {
            return response()->json([
                'city' => $city,
                'state' => $city->state,
            ]);
        }

        return response()->json(null, 404);
    }
}

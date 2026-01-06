<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Firm;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;

class DataController extends Controller
{
    /**
     * Get a list of active firms.
     */
    public function getFirms()
    {
        $firms = Firm::where('is_active', true)->get(['id', 'name']);
        return response()->json($firms);
    }

    /**
     * Get a list of all states.
     */
    public function getStates()
    {
        $states = State::orderBy('name')->get(['id', 'name']);
        return response()->json($states);
    }

    /**
     * Get a list of cities for a given state.
     */
    public function getCities(State $state)
    {
        $cities = $state->cities()->orderBy('name')->get(['id', 'name', 'pincode']);
        return response()->json($cities);
    }

    /**
     * Get location details by pincode.
     */
    public function getLocationByPincode($pincode)
    {
        $city = City::with('state:id,name')->where('pincode', $pincode)->first(['id', 'name', 'state_id']);

        if ($city) {
            return response()->json([
                'city' => $city,
                'state' => $city->state,
            ]);
        }

        return response()->json(['message' => 'Location not found'], 404);
    }
}

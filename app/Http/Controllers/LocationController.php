<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Municipality;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getdistricts(Request $request) {

        $province_id = $request->province_id;

        $districts = District::where('province_id', $province_id)->get();

        return response()->json($districts);

    }

    public function getMunicipalities(Request $request) {

        $province_id = $request->province_id;
        $district_id = $request->district_id;

        $municipalities = Municipality::where('province_id', $province_id)->where('district_id', $district_id)->get();


        return response()->json($municipalities);
    }

    public function getBarangays(Request $request) {
        $municipality_id = $request->municipality_id;
        $barangays = \App\Models\Barangay::where('municipality_id', $municipality_id)->get();
        
        return response()->json($barangays);
    }
}

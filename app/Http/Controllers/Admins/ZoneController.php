<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    public function index() {
        $zones = Zone::orderBy('id', 'desc')->get();

        return response()->json([
            'data' => $zones,
            'message' => 'Successful'
        ]);
    }

    public function view($id) {
        $zone = Zone::find($id);

        if(!$zone) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Bank not found!'
            ], 422);
        }

        return response()->json([
            'data' => $zone,
            'message' => 'Zone loaded successfully!'
        ]);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'duration_1' => 'required|numeric',
            'roi_1' => 'required|numeric',
            'duration_2' => 'required|numeric',
            'roi_2' => 'required|numeric',
            'duration_3' => 'required|numeric',
            'roi_3' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        $exists = Zone::where('name', $validated['name'])->exists();

        if ($exists) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Zone already exists.'
            ], 422);
        }

        $zone = Zone::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'duration_1' => $validated['duration_1'],
            'roi_1' => $validated['roi_1'],
            'duration_2' => $validated['duration_2'],
            'roi_2' => $validated['roi_2'],
            'duration_3' => $validated['duration_3'],
            'roi_3' => $validated['roi_3'],
        ]);


        return response()->json([
            'data' => $zone,
            'message' => 'Zone created successfully!'
        ]);



    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'zone_id' => 'required|numeric',
            'name' => 'required|string',
            'description' => 'required|string',
            'duration_1' => 'required|numeric',
            'roi_1' => 'required|numeric',
            'duration_2' => 'required|numeric',
            'roi_2' => 'required|numeric',
            'duration_3' => 'required|numeric',
            'roi_3' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $zone = Zone::find($validated['zone_id']);

        if(!$zone) {
            return response()->json([
                "error" => 'Crypto not found',
                'message' => 'Crypto not found'
            ], 422);
        }

        $zone->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'duration_1' => $validated['duration_1'],
            'roi_1' => $validated['roi_1'],
            'duration_2' => $validated['duration_2'],
            'roi_2' => $validated['roi_2'],
            'duration_3' => $validated['duration_3'],
            'roi_3' => $validated['roi_3'],
        ]);


        return response()->json([
            'data' => $zone,
            'message' => 'Zone updated successfully!'
        ]);
    }

    public function delete (Request $request) {
        $validator = Validator::make($request->all(), [
            'zone_id' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();


        Zone::find($validated['zone_id'])->forceDelete();

        return $this->index();

    }

    public function status (Request $request) {
        $validator = Validator::make($request->all(), [
            'zone_id' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        $zone = Zone::find($validated['zone_id']);

        if($zone->status === 0) {
            $zone->status = 1;
        }else{
            $zone->status = 0;
        }

        if(!$zone->save()) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        return $this->index();
    }
}

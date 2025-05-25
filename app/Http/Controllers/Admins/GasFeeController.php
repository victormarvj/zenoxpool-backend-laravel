<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\GasFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GasFeeController extends Controller
{
    // public function index() {
    //     $gasFee = GasFee::all();

    //     return response()->json([
    //         'data' => $gasFee,
    //         'message' => 'Successful'
    //     ]);
    // }

    public function view($id) {
        $gasFee = GasFee::find($id);

        if(!$gasFee) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Gas Fee not found!'
            ]);
        }

        return response()->json([
            'data' => $gasFee,
            'message' => 'Gas fee loaded successfully!'
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'gasfee_id' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $gasFee = GasFee::find($validated['gasfee_id']);

        if(!$gasFee) {
            return response()->json([
                "error" => 'Gas fee not found',
                'message' => 'Gas fee not found'
            ], 422);
        }

        $gasFee->update([
            'amount' => $validated['amount'],
        ]);


        return response()->json([
            'data' => $gasFee,
            'message' => 'Gas fee updated successfully!'
        ]);
    }
}

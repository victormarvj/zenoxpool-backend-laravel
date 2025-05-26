<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Crypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CryptoController extends Controller
{
    public function index() {
        $crypto = Crypto::orderBy('id', 'desc')->get();
;

        $crypto->map(function($crypto) {
            $crypto->name = ucfirst($crypto->name);
            $crypto->abbreviation = strtoupper($crypto->abbreviation);
            $crypto->network = strtoupper($crypto->network);
            $crypto->value = number_format($crypto->value, 3);
        });

        return response()->json([
            'data' => $crypto,
            'message' => 'Crypto loaded successfully!'
        ]);
    }

    public function view(Request $request) {
        $validator = Validator::make($request->all(), [
            'crypto_id' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $crypto = Crypto::find($validated['crypto_id']);

        if(!$crypto) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Crypto not found'
            ], 422);
        }

        return response()->json([
            'data' => $crypto,
            'message' => 'Crypto loaded successfully!'
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'crypto_id' => 'required|numeric',
            'name' => 'required|string',
            'abbreviation' => 'required|string',
            'network' => 'required|string',
            'address' => 'required|string',
            'value' => 'required|numeric',
            'image' => 'required|string',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $crypto = Crypto::find($validated['crypto_id']);

        if(!$crypto) {
            return response()->json([
                "error" => 'Crypto not found',
                'message' => 'Crypto not found'
            ], 422);
        }


        $crypto->update([
            'name' => $validated['name'],
            // 'abbreviation' => $validated['abbreviation'],
            'network' => $validated['network'],
            'address' => $validated['address'],
            'value' => $validated['value'],
            'image' => $validated['image'],
        ]);


        return response()->json([
            'data' => $crypto,
            'message' => 'Crypto updated successfully!'
        ]);

    }

    public function uploadFile(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|image|max:2048', // Accept only images
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        // Handle file upload
        if (!$request->hasFile('image')) {
            return response()->json([
                "error" => 'Image file not found.',
                'message' => 'No image found'
            ], 422);
        }

        $imagePath = $request->file('image')->store('crypto_images', 'public');

        return response()->json([
            'data' => $imagePath,
            'message' => 'Image loaded successfully'
        ]);
    }

}

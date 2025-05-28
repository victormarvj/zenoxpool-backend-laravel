<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CodeController extends Controller
{
    public function index() {
        $codes = Code::all();

        return response()->json([
            'data' => $codes,
            'message' => 'Codes loaded successfully'
        ]);
    }

     public function view($id) {

        $code = Code::find($id);

        if(!$code) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Code not found'
            ], 422);
        }

        return response()->json([
            'data' => $code,
            'message' => 'Code loaded successfully!'
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'code_id' => 'required|numeric',
            'name' => 'required|string',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $code = Code::find($validated['code_id']);

        if(!$code) {
            return response()->json([
                "error" => 'Code not found',
                'message' => 'Code not found'
            ], 422);
        }


        $code->update([
            'name' => $validated['name'],
        ]);


        return response()->json([
            'data' => $code,
            'message' => 'Code updated successfully!'
        ]);

    }
}

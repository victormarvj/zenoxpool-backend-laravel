<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    public function index() {
        $banks = Bank::orderBy('id', 'desc')->get();
;

        return response()->json([
            'data' => $banks,
            'message' => 'Bank loaded successfully!'
        ]);
    }

    public function view($id) {
        $bank = Bank::find($id);

        if(!$bank) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Bank not found!'
            ], 422);
        }

        return response()->json([
            'data' => $bank,
            'message' => 'Bank loaded successfully!'
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required|numeric',
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $bank = Bank::find($validated['bank_id']);

        if(!$bank) {
            return response()->json([
                "error" => 'Bank not found',
                'message' => 'Bank not found'
            ], 422);
        }

        $bank->update([
            'bank_name' => $validated['bank_name'],
            'account_name' => $validated['account_name'],
            'account_number' => $validated['account_number'],
        ]);


        return response()->json([
            'data' => $bank,
            'message' => 'Bank updated successfully!'
        ]);
    }
}
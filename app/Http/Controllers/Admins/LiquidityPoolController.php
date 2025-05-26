<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\LiquidityPool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LiquidityPoolController extends Controller
{
    public function view($id) {
        $liquidityPool = LiquidityPool::find($id);

        if(!$liquidityPool) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Liquidity Pool not found!'
            ], 422);
        }

        return response()->json([
            'data' => $liquidityPool,
            'message' => 'Liquidity Pool loaded successfully!'
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'liquiditypool_id' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $liquidityPool = LiquidityPool::find($validated['liquiditypool_id']);

        if(!$liquidityPool) {
            return response()->json([
                "error" => 'Liquidity Pool not found',
                'message' => 'Liquidity Pool not found'
            ], 422);
        }

        $liquidityPool->update([
            'amount' => $validated['amount'],
        ]);


        return response()->json([
            'data' => $liquidityPool,
            'message' => 'Liquidity Pool updated successfully!'
        ]);
    }
}
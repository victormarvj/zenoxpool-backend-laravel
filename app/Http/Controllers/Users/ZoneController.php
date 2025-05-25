<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\LiquidityPool;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();

        $zones = Zone::all();
        $liquidityPool = LiquidityPool::first();

        $liquidityPool->amount = number_format($liquidityPool->amount, 2);

        return response()->json([
            'data' => [
                'user' => $user,
                'liquidityPool' => $liquidityPool,
                'zones' => $zones
            ],
            'message' => 'Successful'
        ]);
    }
}
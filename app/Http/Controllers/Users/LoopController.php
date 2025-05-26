<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoopController extends Controller
{
    public function index(Request $request, $id) {
        $user = $request->user();

        $zone = Zone::find($id);

        if(!$zone) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Zone not found!'
            ], 422);
        }

        $circulation = $user->circulations;

        $user->balance = number_format($user->usdt, 2);

        return response()->json([
            'data' => [
                'user' => $user,
                'zone' => $zone,
                'circulations' => $circulation,
            ],
            'message' => 'Successful'
        ]);
    }
}

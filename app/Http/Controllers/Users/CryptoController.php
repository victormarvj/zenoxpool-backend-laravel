<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Crypto;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    public function index() {
        $cryptos = Crypto::where('status', 1)->get();

        return response()->json([
            'data' => $cryptos,
            'message' => 'Cryptos retrieved successfully!'
        ]);
    }
}
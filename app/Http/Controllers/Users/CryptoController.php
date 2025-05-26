<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Crypto;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    public function index() {
        $crypto = Crypto::orderBy('id', 'desc')->get()->map(function($crypto) {
            $crypto->name = ucfirst($crypto->name);
            $crypto->abbreviation = strtoupper($crypto->abbreviation);
            $crypto->network = strtoupper($crypto->network);
            $crypto->value = number_format($crypto->value, 3);

            return $crypto;
        });

        return response()->json([
            'data' => $crypto,
            'message' => 'Crypto loaded successfully!'
        ]);
    }

    public function view($id) {

        $crypto = Crypto::find($id);

        if(!$crypto) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Crypto not found'
            ], 422);
        }

        $crypto->name = ucfirst($crypto->name);
        $crypto->network = strtoupper($crypto->network);

        return response()->json([
            'data' => $crypto,
            'message' => 'Crypto loaded successfully!'
        ]);
    }
}

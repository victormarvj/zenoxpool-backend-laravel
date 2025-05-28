<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Crypto;
use App\Models\GasFee;
use App\Models\TempTransaction;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();


        $cryptos = Crypto::where('status', 1)->get();

        $gasFee = GasFee::first()->amount;

        $tempTransaction = TempTransaction::where('user_id', $user->id)->first();


        // Get crypto values safely
        $btcValue = optional($cryptos->firstWhere('abbreviation', 'btc'))->value ?? 0;
        $ethValue = optional($cryptos->firstWhere('abbreviation', 'eth'))->value ?? 0;
        $usdtValue = optional($cryptos->firstWhere('abbreviation', 'usdt'))->value ?? 0;
        $bnbValue = optional($cryptos->firstWhere('abbreviation', 'bnb'))->value ?? 0;

        // Sum user balances in USD
        $userBTCValue = $user->btc * $btcValue;
        $userETHValue = $user->eth * $ethValue;
        $userUSDTValue = $user->usdt * $usdtValue;
        $userBNBValue = $user->bnb * $bnbValue;

        $totalSumUSD = $userBTCValue + $userETHValue + $userUSDTValue + $userBNBValue;

        $user->btc = number_format($user->btc, 3);
        $user->usdt = number_format($user->usdt, 3);
        $user->eth = number_format($user->eth, 3);
        $user->bnb = number_format($user->bnb, 3);


        $cryptos->map(function($crypto) {
            $crypto->value = number_format($crypto->value, 2);
        });

        return response()->json([
            'data' => [
                'userBTCValue' => number_format($userBTCValue, 2),
                'userETHValue' => number_format($userETHValue, 2),
                'userUSDTValue' => number_format($userUSDTValue, 2),
                'userBNBValue' => number_format($userBNBValue, 2),
                'user' => $user,
                'crypto' => $cryptos,
                'gasFee' => $gasFee,
                'tempTransaction' => $tempTransaction,
                'totalSum' => number_format($totalSumUSD, 2),
            ],
            'message' => 'Successful'
        ]);
    }
}

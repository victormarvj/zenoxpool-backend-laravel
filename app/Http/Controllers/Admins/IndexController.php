<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Crypto;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index() {
        $users = User::where('privilege', 6)->where('status', 1)->get();
        $banks = Bank::where('status', 1)->get();
        $cryptos = Crypto::where('status', 1)->get();
        $zones = Zone::where('status', 1)->get();

        $userCount = $users->count();
        $bankCount = $banks->count();
        $cryptoCount = $cryptos->count();
        $zoneCount = $zones->count();

        // Get crypto values safely
        $btcValue = optional($cryptos->firstWhere('abbreviation', 'btc'))->value ?? 0;
        $ethValue = optional($cryptos->firstWhere('abbreviation', 'eth'))->value ?? 0;
        $usdtValue = optional($cryptos->firstWhere('abbreviation', 'usdt'))->value ?? 0;
        $bnbValue = optional($cryptos->firstWhere('abbreviation', 'bnb'))->value ?? 0;

        // Sum user balances in USD
        $usersBTC = $users->sum('btc') * $btcValue;
        $usersETH = $users->sum('eth') * $ethValue;
        $usersUSDT = $users->sum('usdt') * $usdtValue;
        $usersBNB = $users->sum('bnb') * $bnbValue;

        $totalSumUSD = $usersBTC + $usersETH + $usersUSDT + $usersBNB;

        return response()->json([
            'data' => [
                'userCount' => number_format($userCount),
                'bankCount' => number_format($bankCount),
                'cryptoCount' => number_format($cryptoCount),
                'zoneCount' => number_format($zoneCount),
                // 'btcValue' => $btcValue,
                // 'ethValue' => $ethValue,
                // 'usdtValue' => $usdtValue,
                // 'bnbValue' => $bnbValue,
                // 'usersBTC' => $usersBTC,
                // 'usersETH' => $usersETH,
                // 'usersUSDT' => $usersUSDT,
                // 'usersBNB' => $usersBNB,
                'totalSum' => number_format($totalSumUSD, 2),
            ],
            'message' => 'Successful'
        ]);
    }


}

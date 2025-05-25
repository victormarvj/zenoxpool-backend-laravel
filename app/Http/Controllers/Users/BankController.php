<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function view() {
        $bank = Bank::first();

        $bank->bank_name = ucwords($bank->bank_name);
        $bank->account_name = ucwords($bank->account_name);

        return response()->json([
            'data' => $bank,
            'message' => 'Bank details retrieved successfully!'
        ]);
    }
}

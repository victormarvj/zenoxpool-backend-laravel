<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index() {

        $transactions = Transaction::join('users', 'transactions.user_id', '=', 'users.id')
            ->select('transactions.*', 'users.username', 'users.email')->get()->map(function($trans) {
            $trans->type_amount = number_format($trans->type_amount, 2);
            $trans->amount = number_format($trans->amount, 2);
            $trans->name = ucwords($trans->name);
            $trans->username = strtolower($trans->username);
            $trans->email = strtolower($trans->email);

            return $trans;
        });


        return response()->json([
            'data' => $transactions,
            'message' => 'transactions retrieved successfully!'
        ]);
    }
}
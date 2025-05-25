<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Mail\AdminDepositConfirmation;
use App\Models\Bank;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request) {
        $transactions = $request->user()->transactions()->orderBy('id', 'desc')->get()->map(function($trans) {
            $trans->type_amount = number_format($trans->type_amount, 2);
            $trans->amount = number_format($trans->amount, 2);
            $trans->name = ucwords($trans->name);

            return $trans;
        });


        return response()->json([
            'data' => $transactions,
            'message' => 'transactions retrieved successfully!'
        ]);
    }

    public function bankDeposit(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        $user = $request->user();

        $bank = Bank::first();

        $transaction_id = 'TXN-' . strtoupper(uniqid(date('YmdHis') . '-'));

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'transaction_id' => $transaction_id,
            'type' => 0,
            'name' => $bank->bank_name,
            'type_amount' => $validated['amount'],
            'amount' => $validated['amount'],
            'address' => $bank->account_number,
        ]);

        Mail::to($request->user())->send(new AdminDepositConfirmation($transaction, $user));


        return response()->json([
            'data' => $transaction,
            'message' => 'Transaction sent successfully. Please wait for confirmation!'
        ]);
    }
}
<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Mail\AdminDepositConfirmation;
use App\Mail\AdminTransferConfirmation;
use App\Mail\TransactionDetails;
use App\Models\Bank;
use App\Models\Crypto;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'name' => $bank->bank_name.'/USD',
            'type_amount' => $validated['amount'],
            'type_name' => 'usdt',
            'amount' => $validated['amount'],
            'address' => $bank->account_number,
        ]);

        Mail::to('support@zenoxpool.com')->send(new AdminDepositConfirmation($transaction, $user));

        Mail::to($user)->send(new TransactionDetails($transaction, $user));


        return response()->json([
            'data' => $transaction,
            'message' => 'Transaction sent successfully. Please wait for confirmation!'
        ]);
    }

    public function cryptoDeposit(Request $request) {
        $validator = Validator::make($request->all(), [
            'abbreviation' => 'required|string',
            'amount' => 'required|numeric',
            'type_amount' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        $user = $request->user();

        $crypto = Crypto::where('abbreviation', $validated['abbreviation'])->first();

        if(!$crypto) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Error verifying crypto data'
            ], 422);
        }

        $transaction_id = 'TXN-' . strtoupper(uniqid(date('YmdHis') . '-'));

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'transaction_id' => $transaction_id,
            'type' => 1,
            'name' => $crypto->name.'/'.$crypto->network,
            'type_amount' => $validated['type_amount'],
            'type_name' => $crypto->abbreviation,
            'amount' => $validated['amount'],
            'address' => $crypto->address,
        ]);

        Mail::to('support@zenoxpool.com')->send(new AdminDepositConfirmation($transaction, $user));

        Mail::to($user)->send(new TransactionDetails($transaction, $user));


        return response()->json([
            'data' => $transaction,
            'message' => 'Transaction sent successfully. Please wait for confirmation!'
        ]);
    }

    public function cryptoTransfer(Request $request) {
        $validator = Validator::make($request->all(), [
            'crypto_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'type_amount' => 'required|numeric',
            'address' => 'required|string',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        $user = $request->user();


        $crypto = Crypto::find($validated['crypto_id']);

        if(!$crypto) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Error verifying crypto data'
            ], 422);
        }

        $balance = $user[$crypto->abbreviation];

        if($balance < $validated['type_amount']) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Insufficienet balance. Please top up account'
            ], 422);
        }


        try {
            DB::transaction(function() use ($user, $crypto, $validated) {

                $transaction_id = 'TXN-' . strtoupper(uniqid(date('YmdHis') . '-'));

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'transaction_id' => $transaction_id,
                    'type' => 2,
                    'name' => $crypto->name.'/'.$crypto->network,
                    'type_amount' => $validated['type_amount'],
                    'type_name' => $crypto->abbreviation,
                    'amount' => $validated['amount'],
                    'address' => $validated['address'],
                ]);


                if(!$transaction) {
                    throw new \Exception('Execution error: 01 Try again. ');
                }

                $user->decrement("$crypto->abbreviation", $validated['type_amount']);


                if(!$user) {
                    throw new \Exception('Execution error: 02 Try again. ');
                }

                Mail::to('support@zenoxpool.com')->send(new AdminTransferConfirmation($transaction, $user));

                Mail::to($user)->send(new TransactionDetails($transaction, $user));

            });

            return response()->json([
                'data' => $user,
                'message' => 'Transaction sent successfully. Please wait for confirmation!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e,
                'message' => 'Execution error. Please try again'
            ], 422);
        }

    }
}

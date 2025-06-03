<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Mail\TransactionDetails;
use App\Models\Crypto;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index() {

        $transactions = Transaction::join('users', 'transactions.user_id', '=', 'users.id')
            ->select('transactions.*', 'users.username', 'users.email')->orderBy('id', 'desc')->get()->map(function($trans) {
            $trans->type_amount = number_format($trans->type_amount, 5);
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

    public function acceptBankDeposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|numeric|exists:transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $transaction = Transaction::find($validated['transaction_id']);

        if (!$transaction) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Invalid transaction data'
            ], 422);
        }

        try {
            DB::transaction(function () use ($transaction) {
                $user = User::lockForUpdate()->find($transaction->user_id);

                if (!$user) {
                    throw new \Exception('User not found');
                }

                $user->increment('usdt', $transaction->type_amount);

                $transaction->update([
                    "status" => 1
                ]);


                Mail::to($user)->send(new TransactionDetails($transaction, $user));

            });

            return $this->index();

        } catch (\Exception $e) {
            return response()->json([
                "error" => 'Error',
                "message" => $e->getMessage()
            ]);
        }
    }


    public function acceptCryptoDeposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|numeric|exists:transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $transaction = Transaction::find($validated['transaction_id']);

        if (!$transaction) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Invalid transaction data'
            ], 422);
        }

        try {
            DB::transaction(function () use ($transaction) {
                $user = User::lockForUpdate()->find($transaction->user_id);

                if (!$user) {
                    throw new \Exception('User not found');
                }

                $user->increment("$transaction->type_name", $transaction->type_amount);

                $transaction->update([
                    "status" => 1
                ]);


                Mail::to($user)->send(new TransactionDetails($transaction, $user));

            });

            return $this->index();

        } catch (\Exception $e) {
            return response()->json([
                "error" => 'Error',
                "message" => $e->getMessage()
            ]);
        }
    }

    public function rejectDeposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|numeric|exists:transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $transaction = Transaction::find($validated['transaction_id']);

        if (!$transaction) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Invalid transaction data'
            ], 422);
        }

        $transaction->update(["status" => 2]);

        $user = User::find($transaction->user_id);


        Mail::to($user)->send(new TransactionDetails($transaction, $user));

        return $this->index();
    }

    public function acceptCryptoTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|numeric|exists:transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $transaction = Transaction::find($validated['transaction_id']);

        if (!$transaction) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Invalid transaction data'
            ], 422);
        }

        $transaction->update(["status" => 1]);

        $user = User::find($transaction->user_id);

        Mail::to($user)->send(new TransactionDetails($transaction, $user));

        return $this->index();
    }

    public function rejectCryptoTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|numeric|exists:transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $transaction = Transaction::find($validated['transaction_id']);

        if (!$transaction) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Invalid transaction data'
            ], 422);
        }

        DB::transaction(function () use ($transaction) {
            $transaction->update(["status" => 2]);

            // Increment user balance
            User::find($transaction->user_id)->increment($transaction->type_name, $transaction->type_amount);

            // Refresh user and transaction from DB
            $user = User::find($transaction->user_id);
            $freshTransaction = Transaction::find($transaction->id);

            if (!$user) {
                throw new \Exception('User not found');
            }

            // Send mail
            Mail::to($user->email)->send(new TransactionDetails($freshTransaction, $user));
        });

        return $this->index();
    }

    public function createHistory(Request $request) {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'type' => 'required|numeric',
            'abbreviation' => 'required|string',
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

        $user = User::find($validated['user_id']);


        if(!$user) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Error verifying user data'
            ], 422);
        }

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
            'type' => $validated['type'],
            'name' => $crypto->name.'/'.$crypto->network,
            'type_amount' => $validated['type_amount'],
            'type_name' => $crypto->abbreviation,
            'amount' => $validated['amount'],
            'address' => $validated['address'],
            'status' => 1,
        ]);


        return response()->json([
            'data' => $transaction,
            'message' => 'Transaction history created successfully!'
        ]);
    }

}
<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Mail\AdminTransferConfirmation;
use App\Mail\TransactionDetails;
use App\Models\Code;
use App\Models\Crypto;
use App\Models\TempTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TempTransactionController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();

        $transaction = TempTransaction::where('user_id', $user->id)->first();

        return response()->json([
            'data' => $transaction,
            'message' => 'You have a pending transaction. Please proceed to complete previous transaction.'
        ]);
    }

    public function view(Request $request, $id) {
        $user = $request->user();
        $transaction = TempTransaction::where('user_id', $user->id)->where('id', $id)->first();

        if(!$transaction) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Transaction not found!'
            ], 422);
        }

        $code = Code::where('code_position', $transaction->no_of_codes)->first();

        if(!$code) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Invalid transaction data!'
            ], 422);
        }

        $code->name = ucwords($code->name);

        return response()->json([
            'data' => [
                'transaction' => $transaction,
                'code' => $code,
                'user' => $user
            ],
            'message' => 'Transaction loaded successfully'
        ]);
    }

    public function verifyCode(Request $request) {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|numeric',
            'code' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $user = $request->user();

        $transaction = TempTransaction::where('user_id', $user->id)->where('id', $validated['transaction_id'])->first();

        if(!$transaction) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Invalid transaction data!'
            ], 422);
        }


        $transaction->decrement('no_of_codes', 1);

        return response()->json([
            'data' => $transaction,
            'message' => 'Transaction code verified. Please continue transaction'
        ]);

    }

    public function processTempTransfer(Request $request) {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|numeric',
            'code' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        $user = $request->user();

        $tempTrans = TempTransaction::where('user_id', $user->id)->where('id', $validated['transaction_id'])->first();


        if(!$tempTrans) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Invalid transaction data!'
            ], 422);
        }


        $crypto = Crypto::find($tempTrans->crypto_id);

        if(!$crypto) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Error verifying crypto data'
            ], 422);
        }


        try {
            DB::transaction(function() use ($user, $crypto, $tempTrans) {


                // $oldTrans = $tempTrans->toArray();
                // unset($oldTrans['crypto_id']);
                // unset($oldTrans['no_of_codes']);

                $transaction_id = 'TXN-' . strtoupper(uniqid(date('YmdHis') . '-'));

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'transaction_id' => $transaction_id,
                    'type' => 2,
                    'name' => $crypto->name.'/'.$crypto->network,
                    'type_amount' => $tempTrans->type_amount,
                    'type_name' => $crypto->abbreviation,
                    'amount' => $tempTrans->amount,
                    'address' => $tempTrans->address,
                ]);


                if(!$transaction) {
                    throw new \Exception('Execution error: 01 Try again. ');
                }

                $tempTrans->forceDelete();

                $codeArray = array_map(function() {
                    return str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                }, range(1, 5));

                $user->code_1 = $codeArray[0];
                $user->code_2 = $codeArray[1];
                $user->code_3 = $codeArray[2];
                $user->code_4 = $codeArray[3];
                $user->code_5 = $codeArray[4];

                $user->save();

                $user->decrement("$crypto->abbreviation", $transaction->type_amount);


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

    public function deleteTempTransfer(Request $request, $id) {

        $user = $request->user();

        $transaction = TempTransaction::where('user_id', $user->id)->where('id', $id)->first();

        if(!$transaction) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Invalid transaction data!'
            ], 422);
        }


        $transaction->forceDelete();

        return response()->json([
            'data' => $transaction,
            'message' => 'Transaction terminated! no proper verification was provided.'
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



        $transaction_id = 'TXN-' . strtoupper(uniqid(date('YmdHis') . '-'));

        $transaction = TempTransaction::create([
            'user_id' => $user->id,
            'transaction_id' => $transaction_id,
            'crypto_id' => $crypto->id,
            'type' => 2,
            'name' => $crypto->name.'/'.$crypto->network,
            'type_amount' => $validated['type_amount'],
            'type_name' => $crypto->abbreviation,
            'amount' => $validated['amount'],
            'address' => $validated['address'],
            'no_of_codes' => $user->no_of_codes,
        ]);


        if(!$transaction) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Execution error. Please try again'
            ], 422);
        }

        return response()->json([
            'data' => $transaction,
            'message' => 'Transaction incomplete. Verification needed!'
        ]);

    }
}

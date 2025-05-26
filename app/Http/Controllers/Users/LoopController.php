<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Circulation;
use App\Models\Crypto;
use App\Models\GasFee;
use App\Models\Transaction;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoopController extends Controller
{
    public function index(Request $request, $id) {
        $user = $request->user();

        $zone = Zone::find($id);

        if(!$zone) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Zone not found!'
            ], 422);
        }

        $circulation = $user->circulations;

        $user->balance = number_format($user->usdt, 2);

        $gasFee = GasFee::first()->amount;

        return response()->json([
            'data' => [
                'user' => $user,
                'zone' => $zone,
                'circulations' => $circulation,
                'gasFee' => $gasFee
            ],
            'message' => 'Successful'
        ]);
    }

    public function circulate(Request $request) {
        $validator = Validator::make($request->all(), [
            'zone_id' => 'required|numeric',
            'duration' => 'required|numeric',
            'amount' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        $zone = Zone::find($validated['zone_id']);

        if(!$zone) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Zone not found!'
            ], 422);
        }


        $crypto = Crypto::where('abbreviation', 'usdt')->first();

        if(!$crypto) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Error verifying crypto data'
            ], 422);
        }


        $user = $request->user();
        $gasFee = GasFee::first()->amount;

        $totalAmount = $validated['amount']+$gasFee;

        if($user->usdt < $totalAmount) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Insufficient USDT balance. Please top up or swap to get USDT'
            ], 422);
        }

        $transaction_id = 'TXN-' . strtoupper(uniqid(date('YmdHis') . '-'));

        try {
            DB::transaction(function() use ($user, $totalAmount, $zone, $validated, $crypto, $transaction_id, $gasFee) {

                $circulation = Circulation::create([
                    'user_id' => $user->id,
                    'zone_id' => $zone->id,
                    'duration' => $validated['duration'],
                    'amount' => $validated['amount'],
                    'total' => $validated['total'],
                ]);

                if(!$circulation) {
                    throw new \Exception('Execution error: 01 Try again. ');
                }

                $user->decrement('usdt', $totalAmount);

                if(!$user) {
                    throw new \Exception('Execution error: 02 Try again. ');
                }


                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'transaction_id' => $transaction_id,
                    'type' => 5,
                    'name' => $crypto->name.'/'.$crypto->network,
                    'type_amount' => $validated['amount'],
                    'type_name' => $crypto->abbreviation,
                    'amount' => $validated['amount'],
                    'address' => $crypto->address,
                    'status' => 1
                ]);

                if(!$transaction) {
                    throw new \Exception('Execution error: 03 Try again. ');
                }


                $transaction_id = 'TXN-' . strtoupper(uniqid(date('YmdHis') . '-'));

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'transaction_id' => $transaction_id,
                    'type' => 6,
                    'name' => $crypto->name.'/'.$crypto->network,
                    'type_amount' => $gasFee,
                    'type_name' => $crypto->abbreviation,
                    'amount' => $gasFee,
                    'address' => $crypto->address,
                    'status' => 1
                ]);

                if(!$transaction) {
                    throw new \Exception('Execution error: 04 Try again. ');
                }

            });

            return $this->index($request, $zone->id);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e,
                'message' => 'Execution error. Please try again'
            ], 422);
        }

    }

    public function completed(Request $request, $zone_id, $id) {
        $validator = Validator::make($request->all(), [
            'loop_id' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        if($validated['loop_id'] != $id) {
            return response()->json([
                "error" => 'Error',
                'message' => 'No data integrity'
            ], 422);
        }


        // Get your single record
        $circulation = Circulation::find($validated['loop_id']);


        if(!$circulation) {
            return response()->json([
                "error" => 'Error',
                'message' => 'No data match this request'
            ], 422);
        }


        $crypto = Crypto::where('abbreviation', 'usdt')->first();

        // Compare: (created_at + duration days) <= now()

        if ($circulation->created_at->addDays($circulation->duration) > now()) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Still in circulation'
            ], 422);
        }

        // if ($circulation->created_at->addSeconds($circulation->duration) > now()) {
        //     return response()->json([
        //         "error" => 'Error',
        //         'message' => 'Still in circulation'
        //     ], 422);
        // }


        $user = $request->user();

        if($circulation->status == 0) {
            try {
                DB::transaction(function() use ($user, $circulation, $crypto) {
                    $user->increment('usdt', $circulation->total);

                    if(!$user) {
                        throw new \Exception('Execution error: 01 Try again.');
                    }

                    $circulation->update(['status' => 1]);

                    if(!$circulation) {
                        throw new \Exception('Execution error: 02 Try again. ');
                    }

                    $transaction_id = 'TXN-' . strtoupper(uniqid(date('YmdHis') . '-'));

                    $transaction = Transaction::create([
                        'user_id' => $user->id,
                        'transaction_id' => $transaction_id,
                        'type' => 1,
                        'name' => $crypto->name.'/'.$crypto->network,
                        'type_amount' => $circulation->total,
                        'type_name' => $crypto->abbreviation,
                        'amount' => $circulation->total,
                        'address' => $crypto->address,
                        'status' => 1
                    ]);


                    if(!$transaction) {
                        throw new \Exception('Execution error: 03 Try again. ');
                    }
                });

                return $this->index($request, $zone_id);

            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e,
                    'message' => 'Execution error. Please try again'
                ], 422);
            }
        }


        return $this->index($request, $zone_id);


    }
}

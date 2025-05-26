<?php

use App\Http\Controllers\Admins\BankController;
use App\Http\Controllers\Admins\CryptoController;
use App\Http\Controllers\Admins\GasFeeController;
use App\Http\Controllers\Admins\IndexController;
use App\Http\Controllers\Admins\LiquidityPoolController;
use App\Http\Controllers\Admins\TransactionController as AdminsTransactionController;
use App\Http\Controllers\Admins\UserController as AdminsUserController;
use App\Http\Controllers\Admins\ZoneController;
use App\Http\Controllers\Users\BankController as UsersBankController;
use App\Http\Controllers\Users\CryptoController as UsersCryptoController;
use App\Http\Controllers\Users\IndexController as UsersIndexController;
use App\Http\Controllers\Users\LoopController;
use App\Http\Controllers\Users\TransactionController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\ZoneController as UsersZoneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::prefix('user')->group(function() {
    Route::controller(UserController::class)->group(function() {
        Route::post('/register', 'register');

        Route::post('/login', 'login');


        Route::middleware('auth:sanctum')->group(function() {


            Route::post('/settings', 'settings');

            Route::prefix('profile')->group(function() {
                Route::get('/', 'profile');
                Route::post('/', 'updateProfile');
                Route::post('/image-upload', 'imageUpload');
            });

            Route::post('/logout', 'logout');
        });

    });


    Route::middleware('auth:sanctum')->group(function() {
        Route::controller(UsersIndexController::class)->group(function() {
            Route::get('overview', 'index');
        });

        Route::controller(UsersZoneController::class)->group(function() {
            Route::prefix('zones')->group(function() {
                Route::get('/', 'index');
            });
        });

        Route::controller(LoopController::class)->group(function() {
            Route::prefix('loop')->group(function() {
                Route::get('/{id}', 'index');
            });
        });

        Route::prefix('bank')->group(function() {
            Route::controller(UsersBankController::class)->group(function() {
                Route::get('/details', 'view');

            });

            Route::controller(TransactionController::class)->group(function() {
                Route::prefix('deposit')->group(function() {
                    Route::post('/', 'bankDeposit');
                });
            });
        });

        Route::controller(TransactionController::class)->group(function() {
            Route::get('transactions', 'index');
        });


        Route::controller(UsersCryptoController::class)->group(function() {
            Route::prefix('crypto')->group(function() {
                Route::get('/', 'index');
                Route::get('/{id}', 'view');


                Route::controller(TransactionController::class)->group(function() {
                    Route::prefix('deposit')->group(function() {
                        Route::post('/', 'cryptoDeposit');
                    });
                });
            });
        });
    });

});

Route::prefix('admin')->group(function() {
    Route::middleware('auth:sanctum')->group(function() {
        Route::controller(AdminsUserController::class)->group(function() {
            Route::prefix('users')->group(function() {
                Route::get('/', 'index');
                Route::post('/change-status', 'status');
                Route::post('/delete-user', 'delete');
                Route::post('/register', 'register');
                Route::get('/edit-user/{id}', 'view');
                Route::post('/edit-user/', 'update');
            });

            Route::post('/settings', 'settings');

            Route::prefix('profile')->group(function() {
                Route::get('/', 'profile');
                Route::post('/', 'updateProfile');
                Route::post('/image-upload', 'imageUpload');
            });
        });

        Route::controller(ZoneController::class)->group(function() {
            Route::prefix('zones')->group(function() {
                Route::get('/', 'index');
                Route::post('/change-status', 'status');
                Route::post('/delete-zone', 'delete');
                Route::post('/register', 'register');
                Route::get('/edit-zone/{id}', 'view');
                Route::post('/edit-zone/', 'update');
            });
        });

        Route::controller(CryptoController::class)->group(function() {
            Route::prefix('cryptos')->group(function() {
                Route::get('/', 'index');
                Route::get('/edit-crypto/{id}', 'view');
                Route::post('/edit-crypto/', 'update');
                Route::post('/image-upload/', 'uploadFile');
            });
        });

        Route::controller(BankController::class)->group(function() {
            Route::prefix('banks')->group(function() {
                Route::get('/', 'index');
                Route::get('/edit-bank/{bank_id}', 'view');
                Route::post('/edit-bank/', 'update');
            });
        });

        Route::controller(LiquidityPoolController::class)->group(function() {
            Route::prefix('liquidity-pool')->group(function() {
                // Route::get('/', 'index');
                Route::get('/edit-liquidity-pool/{id}', 'view');
                Route::post('/edit-liquidity-pool/', 'update');
            });
        });

        Route::controller(GasFeeController::class)->group(function() {
            Route::prefix('gas-fee')->group(function() {
                // Route::get('/', 'index');
                Route::get('/edit-gas-fee/{gasfee_id}', 'view');
                Route::post('/edit-gas-fee/', 'update');
            });
        });

        Route::controller(IndexController::class)->group(function() {
            Route::get('overview', 'index');
        });

        Route::controller(AdminsTransactionController::class)->group(function() {

            Route::prefix('transactions')->group(function() {
                Route::get('/', 'index');

                Route::prefix('bank-deposit')->group(function() {
                    Route::post('accept', 'acceptBankDeposit');
                    Route::post('reject', 'rejectDeposit');
                });

                Route::prefix('crypto-deposit')->group(function() {
                    Route::post('accept', 'acceptCryptoDeposit');
                    Route::post('reject', 'rejectDeposit');
                });
            });
        });

    });;
});

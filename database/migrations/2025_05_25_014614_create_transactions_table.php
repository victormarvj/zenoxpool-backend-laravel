<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('transaction_id');
            $table->unsignedTinyInteger('type')->default(0)->comment('0=bank_deposit, 1=crypto_deposit, 2=transfer, 3=swap, 4=swap-swaped, 5=gas fee');
            $table->string('name')->comment('bitcoin / bank name  ');
            $table->float('type_amount')->comment('in crypto');
            $table->string('type_name')->comment('btc / usdt / eth / bnb');
            $table->float('amount')->comment('in USD');
            $table->string('address')->comment('account number / crypto address / crypto name');
            $table->unsignedTinyInteger('status')->index()->default(0)->comment('0=pending, 1=completed');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
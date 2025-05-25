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
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description');
            $table->unsignedInteger('duration_1');
            $table->float('roi_1');
            $table->unsignedInteger('duration_2');
            $table->float('roi_2');
            $table->unsignedInteger('duration_3');
            $table->float('roi_3');
            $table->unsignedTinyInteger('status')->default(1)->comment('0=inactive, 1=active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
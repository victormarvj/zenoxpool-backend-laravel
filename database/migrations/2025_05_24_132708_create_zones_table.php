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
            $table->unsignedInteger('duration_1')->comment('in days');
            $table->float('roi_1')->comment('in %');
            $table->unsignedInteger('duration_2')->comment('in days');
            $table->float('roi_2')->comment('in %');
            $table->unsignedInteger('duration_3')->comment('in days');
            $table->float('roi_3')->comment('in %');
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

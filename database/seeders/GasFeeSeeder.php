<?php

namespace Database\Seeders;

use App\Models\GasFee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GasFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GasFee::create([
            'amount' => 10,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\LiquidityPool;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LiquidityPoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LiquidityPool::create([
            'amount' => 456983232.45,
        ]);
    }
}

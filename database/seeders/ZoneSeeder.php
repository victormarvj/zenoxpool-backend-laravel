<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Zone::create([
            'name' => 'loop',
            'description' => 'A circulation yield protocol based on the Ethereum Virtual Machine (EVM) enables individuals to provide cryptocurrency liquidity risk-free and earn returns.',
            'duration_1' => '7',
            'roi_1' => '4',
            'duration_2' => '14',
            'roi_2' => '10',
            'duration_3' => '28',
            'roi_3' => '24',
        ]);

        Zone::create([
            'name' => 'savings',
            'description' => 'A cryptocurrency savings protocol basesd on the Binance Smart Chain network, designed to provide users with a secure and efficient savings solution.',
            'duration_1' => '7',
            'roi_1' => '4',
            'duration_2' => '14',
            'roi_2' => '10',
            'duration_3' => '28',
            'roi_3' => '24',
            'status' => 0
        ]);

        Zone::create([
            'name' => 'game',
            'description' => "Aimed at achieving a fair distribution of ZenoxCoin through a presale program, providing sustainable support for the development of Zenox's ecosystem.",
            'duration_1' => '7',
            'roi_1' => '4',
            'duration_2' => '14',
            'roi_2' => '10',
            'duration_3' => '28',
            'roi_3' => '24',
            'status' => 0
        ]);
    }
}
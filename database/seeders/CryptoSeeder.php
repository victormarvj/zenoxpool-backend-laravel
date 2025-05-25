<?php

namespace Database\Seeders;

use App\Models\Crypto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CryptoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Crypto::create([
            'name' => 'bitcoin',
            'abbreviation' => 'btc',
            'network' => 'btc',
            'address' => '0x123abcdefghijkl',
            'value' => '103731.300',
            'image' => 'crypto_images/btc.png',
        ]);

        Crypto::create([
            'name' => 'tether',
            'abbreviation' => 'usdt',
            'network' => 'bep20',
            'address' => '0x123abcdefghijkl',
            'value' => '1.000',
            'image' => 'crypto_images/usdt.png',
        ]);

        Crypto::create([
            'name' => 'ethereum',
            'abbreviation' => 'eth',
            'network' => 'erc20',
            'address' => '0x123abcdefghijkl',
            'value' => '2545.710',
            'image' => 'crypto_images/eth.png',
        ]);

        Crypto::create([
            'name' => 'binance',
            'abbreviation' => 'bnb',
            'network' => 'bep20',
            'address' => '0x123abcdefghijkl',
            'value' => '653.060',
            'image' => 'crypto_images/bnb.png',
        ]);
    }
}

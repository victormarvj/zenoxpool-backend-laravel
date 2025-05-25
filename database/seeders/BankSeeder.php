<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bank::create([
            'bank_name' => 'bank of spain',
            'account_name' => 'tesla & sons',
            'account_number' => '3221234545'
        ]);
    }
}

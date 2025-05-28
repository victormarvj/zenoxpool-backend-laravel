<?php

namespace Database\Seeders;

use App\Models\Code;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Code::create([
            'code_position' => 1,
            'name' => 'transfer code',
        ]);

        Code::create([
            'code_position' => 2,
            'name' => 'VAT code',
        ]);

        Code::create([
            'code_position' => 3,
            'name' => 'activation code',
        ]);

        Code::create([
            'code_position' => 4,
            'name' => 'IMF code',
        ]);

        Code::create([
            'code_position' => 5,
            'name' => 'COT code',
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Credential;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Credential::create([
            'user_id' => '3',
            'password' => 'mmmmmm'
        ]);

        Credential::create([
            'user_id' => '4',
            'password' => 'mmmmmm'
        ]);
    }
}
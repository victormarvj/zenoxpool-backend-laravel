<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'fullname' => 'controller controller',
            'username' => 'controller',
            'email' => 'controller@gmail.com',
            'phone' => '00000000000',
            'code_5' => '1111',
            'code_4' => '2222',
            'code_3' => '3333',
            'code_2' => '4444',
            'code_1' => '5555',
            'password' => Hash::make('c0ntr0ll3r'),
            'privilege' => 0,
        ]);

        User::create([
            'fullname' => 'admin admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '11111111111',
            'code_5' => '1111',
            'code_4' => '2222',
            'code_3' => '3333',
            'code_2' => '4444',
            'code_1' => '5555',
            'password' => Hash::make('@dm1n@123'),
            'privilege' => 1,
        ]);

        User::create([
            'fullname' => 'john doe',
            'username' => 'johndoe',
            'email' => 'johndoe@gmail.com',
            'phone' => '2222222222',
            'code_5' => '1111',
            'code_4' => '2222',
            'code_3' => '3333',
            'code_2' => '4444',
            'code_1' => '5555',
            'password' => Hash::make('mmmmmm'),
            'privilege' => 6,
        ]);

        User::create([
            'fullname' => 'test user',
            'username' => 'testuser',
            'email' => 'testuser@gmail.com',
            'phone' => '33333333333',
            'code_5' => '1111',
            'code_4' => '2222',
            'code_3' => '3333',
            'code_2' => '4444',
            'code_1' => '5555',
            'password' => Hash::make('mmmmmm'),
            'privilege' => 6,
        ]);

    }
}
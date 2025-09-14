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
        $users = [
            [
                'username' => '3ala2_shouman',
                'fname'     => 'Alaa',
                'lname'     => 'Shouman',
                'email'     => 'alaa@example.com',
                'password'  => Hash::make('123456'), // change before production
            ],
            [
                'username' => 'bob',
                'fname'     => 'Bob',
                'lname'     => 'Johnson',
                'email'     => 'bob@example.com',
                'password'  => Hash::make('123456'), // change before production
            ],
        ];

        foreach ($users as $data) {
            // updateOrCreate prevents duplicate seed entries
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}

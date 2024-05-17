<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'lastname' => 'Ungaro',
                'dni' => '39564543',
                'cuit' => '20395645430',
                'address' => 'Calle 532',
                'city' => 'Berazategui',
                'province' => 'Buenos Aires',
                'phone' => '1123432356',
                'email_verified_at' => now(),
                'password' => Hash::make('admin'),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'lucasu',
                'email' => 'lucasu@gmail.com',
                'lastname' => 'Gonzalez',
                'dni' => '39453213',
                'cuit' => '20394532130',
                'address' => 'Calle 134',
                'city' => 'Quilmes',
                'province' => 'Buenos Aires',
                'phone' => '1156342345',
                'email_verified_at' => now(),
                'password' => Hash::make('lucasu'),
                'remember_token' => Str::random(10),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }


    }
}

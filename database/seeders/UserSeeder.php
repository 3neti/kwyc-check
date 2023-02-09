<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $name = 'Lester Hurtado';
        $email = '3neti@lyflyn.net';
        $password = bcrypt('password');

        User::create(compact('name', 'email', 'password'));
    }
}

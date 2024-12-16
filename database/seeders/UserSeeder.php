<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name'=>'Sonya',
                'email'=>'sonkavelik@gmail.com',
                'email_verified_at'=>'2024-11-29 07:45:10',
                'password'=>Hash::make('sonkavelik'),
            ],
        ]);
    }
}

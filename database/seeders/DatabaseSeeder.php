<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//         \App\Models\User::factory(10)->create();

         \App\Models\User::factory()->create([
             'email' => 'admin@.com',
             'password' => 'helloworld',
             'firstName'=>'soufiane',
             'lastName'=>'rabya',
             'phone'=>'0489747746',
             'city'=>'Rabat',
             'role'=>4,
             'address'=>'rabat center ville '
         ]);
    }
}

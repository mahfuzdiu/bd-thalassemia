<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //1 admins
        User::factory()->count(1)->admin()->create();

        //3 managers
        User::factory()->count(3)->vendor()->create();

        //6 customers
        User::factory()->count(6)->create();
    }
}

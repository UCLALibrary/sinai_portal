<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Rafael Schwemmer',
            'email' => 'rafael.schwemmer@textandbytes.com',
        ]);

        User::factory()->create([
            'name' => 'Douglas Kim',
            'email' => 'dougkim@15solutions.com',
        ]);

        User::factory()->create([
            'name' => 'Dawn Childress',
            'email' => 'dchildress@library.ucla.edu',
        ]);

        User::factory()->create([
            'name' => 'William Potter',
            'email' => 'williampotter@library.ucla.edu',
        ]);

        User::factory()->create([
            'name' => 'Lukas Märki',
            'email' => 'lukas@inventic.ch',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Message;
use Faker\Factory as Faker;

class MessageUserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create 10 users with random names and email addresses
        foreach (range(1, 10) as $index) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
            ]);
        }

        // Create 50 messages with random content and associate them with users
        foreach (range(1, 50) as $index) {
            Message::create([
                'users_id' => $faker->numberBetween(1, 10), // User ID between 1 and 10
                'content' => $faker->paragraph,
            ]);
        }
    }
}

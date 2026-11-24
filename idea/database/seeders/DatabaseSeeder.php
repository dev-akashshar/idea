<?php

namespace Database\Seeders;

use App\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password123',
            ]
        );

        if ($user->ideas()->count() === 0) {
            $idea = $user->ideas()->create([
                'title' => 'My First Idea',
                'description' => 'Welcome to your ideas app! Capture your thoughts and make a plan.',
                'status' => IdeaStatus::PENDING,
                'links' => ['https://laravel.com'],
            ]);

            $idea->steps()->createMany([
                ['description' => 'First step to explore'],
                ['description' => 'Second step to complete'],
            ]);
        }
    }
}

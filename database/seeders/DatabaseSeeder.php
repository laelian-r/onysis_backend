<?php

namespace Database\Seeders;

use App\Models\Type;
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
        // User::factory(10)->create();
        // Article::factory()->count(5)->create();
        foreach (['Single', 'EP', 'Album', 'Mixtape', 'Compilation', 'Live', 'Remix'] as $name) {
            Type::firstOrCreate(['type' => $name]);
        }

        User::factory()->create([
            'name' => 'Laélian',
            'email' => 'laelian.roux@gmail.com',
            'password' => 'yh77un!+',
        ]);
    }
}

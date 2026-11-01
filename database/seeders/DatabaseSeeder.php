<?php

namespace Database\Seeders;

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
        $this->call([PegawaiSeeder::class]);
        \App\Models\User::create([
            "nama" => "I'm Not Robot",
            "username" => "admin",
            "password" => bcrypt("admin123"),
            "email" => "admin@gmail.com",
        ]);
    }
}

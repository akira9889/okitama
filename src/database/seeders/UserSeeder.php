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
        User::factory()->create([
            'last_name' => 'テスト',
            'email' => 'test@example.com',
            'is_approved' => true,
            'is_admin' => true,
        ]);

        User::factory(30)->create();
    }
}

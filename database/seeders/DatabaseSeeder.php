<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        User::factory()->create([
            'name' => 'Admin',
            'surname' => 'Test',
            'email' => 'test@example.com',
            'password' => Hash::make('123456')
        ]);
    }
}

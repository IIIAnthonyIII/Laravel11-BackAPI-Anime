<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        User::factory()->create([
            'name' => 'admin',
            'surname' => 'Test',
            'email' => 'test@example.com',
            'password' => Hash::make('12345')
        ]);
        $this->call([TypeSeeder::class,]);
    }
}

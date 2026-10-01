<?php

namespace Database\Seeders;

use App\Enums\Role;
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

    User::factory()->create
     ([
        'name' => 'Admin BULOG',
        'username' => 'admin',
        'email' => 'admin@bulog.test',
        'phone' => '081200000001',
        'role' => Role::Admin,
      ]);

    User::factory()->create
      ([
        'name' => 'Manager BULOG',
        'username' => 'manager',
        'email' => 'manager@bulog.test',
        'phone' => '081200000002',
        'role' => Role::Manager,
      ]);
    }
}

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
        'email' => 'admin@bulog.test',
        'role' => Role::Admin,
      ]);

    User::factory()->create
      ([
        'name' => 'Manager BULOG',
        'email' => 'manager@bulog.test',
        'role' => Role::Manager,
      ]);
    }
}

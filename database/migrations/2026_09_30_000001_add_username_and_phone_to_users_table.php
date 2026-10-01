<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
        });

        // Akun lama diberi username dari bagian depan emailnya agar tetap bisa login.
        foreach (DB::table('users')->whereNull('username')->get() as $user) {
            $base = preg_replace('/[^a-z0-9_-]/', '', Str::lower(Str::before($user->email, '@')));
            $base = Str::substr($base ?: 'user', 0, 24);
            $username = $base;

            for ($i = 1; DB::table('users')->where('username', $username)->exists(); $i++) {
                $username = $base.$i;
            }

            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone']);
        });
    }
};

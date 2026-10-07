<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Desain baru: Kancab/Kanwil diganti Desa/Kelurahan dan Kota/Kabupaten,
 * ditambah Jumlah Penyerapan (disimpan dalam Kg; Ton dihitung dari Kg).
 * Kolom kancab/kanwil tidak dihapus agar data lama aman, hanya dibuat opsional.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_books', function (Blueprint $table) {
            $table->string('village')->default('')->after('mitra_pengolahan');
            $table->string('regency')->default('')->after('village');
            $table->decimal('absorption_kg', 14, 2)->default(0)->after('regency');
        });

        Schema::table('work_books', function (Blueprint $table) {
            $table->string('kancab')->nullable()->change();
            $table->string('kanwil')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('work_books', function (Blueprint $table) {
            $table->dropColumn(['village', 'regency', 'absorption_kg']);
        });
    }
};

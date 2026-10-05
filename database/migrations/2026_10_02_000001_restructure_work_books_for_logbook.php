<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyesuaikan struktur dengan desain Figma:
 * - satu "Tanggal penyerapan" (bukan tanggal mulai & selesai)
 * - bukti (1 video + 3 foto) menempel langsung ke buku kerja, tanpa log_entries
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('log_entries');

        Schema::table('work_books', function (Blueprint $table) {
            $table->renameColumn('start_date', 'absorption_date');
        });

        Schema::table('work_books', function (Blueprint $table) {
            $table->dropColumn(['end_date', 'finished_at']);
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_book_id')->constrained()->cascadeOnDelete();
            $table->string('category', 20);
            $table->string('type', 10);
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->timestamps();
            $table->unique(['work_book_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');

        Schema::table('work_books', function (Blueprint $table) {
            $table->date('end_date')->nullable();
            $table->timestamp('finished_at')->nullable();
        });

        Schema::table('work_books', function (Blueprint $table) {
            $table->renameColumn('absorption_date', 'start_date');
        });
    }
};

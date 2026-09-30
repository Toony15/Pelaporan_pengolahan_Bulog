<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_book_id')->constrained()->cascadeOnDelete();
            $table->date('entry_date');
            $table->text('description');
            $table->decimal('volume_kg', 12, 2)->nullable();
            $table->decimal('moisture_percent', 5, 2)->nullable();
            $table->text('issue')->nullable();
            $table->timestamps();
            $table->index(['work_book_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_entries');
    }
};

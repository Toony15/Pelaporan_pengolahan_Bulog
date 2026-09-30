<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('work_books', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('pic_name');
        $table->string('mitra_pengolahan');
        $table->string('kancab');
        $table->string('kanwil');
        $table->date('start_date');
        $table->date('end_date');
        $table->timestamp('finished_at')->nullable();
        $table->timestamps();
        $table->index(['kanwil', 'kancab']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_books');
    }
};

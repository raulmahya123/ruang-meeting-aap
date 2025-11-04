<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->dateTime('start_at');
            $table->dateTime('end_at');

            $table->string('booked_by_name');

            // SIMPAN DIVISI LANGSUNG DI BOOKINGS (STRING agar bisa ketik bebas / datalist)
            $table->string('division', 50)->nullable();

            $table->string('cancel_token', 64)->unique();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'start_at', 'end_at']);
            $table->index('division');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

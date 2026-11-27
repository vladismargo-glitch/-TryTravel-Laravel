<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table): void {
            $table->bigIncrements('booking_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tour_id');
            $table->unsignedTinyInteger('checking')->default(0);
            $table->dateTime('created_at')->useCurrent();
            $table->index(['user_id', 'created_at'], 'ix_bookings_user_created');
            $table->index('tour_id', 'ix_bookings_tour');
            $table->foreign('user_id', 'fk_bookings_user')->references('user_id')->on('users');
            $table->foreign('tour_id', 'fk_bookings_tour')->references('tour_id')->on('tours');
        });

        Schema::create('favorites', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tour_id');
            $table->dateTime('added_at')->useCurrent();
            $table->primary(['user_id', 'tour_id']);
            $table->index('tour_id', 'ix_favorites_tour');
            $table->foreign('user_id', 'fk_favorites_user')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('tour_id', 'fk_favorites_tour')->references('tour_id')->on('tours')->cascadeOnDelete();
        });

        DB::statement('ALTER TABLE bookings ADD CONSTRAINT ck_booking_checking CHECK (checking IN (0, 1))');
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('bookings');
    }
};

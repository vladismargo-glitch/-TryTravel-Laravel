<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table): void {
            $table->bigIncrements('promotion_id');
            $table->string('title', 200);
            $table->text('description');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->index(['starts_at', 'ends_at'], 'ix_promotions_dates');
        });

        Schema::create('promotion_tours', function (Blueprint $table): void {
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('tour_id');
            $table->primary(['promotion_id', 'tour_id']);
            $table->index('tour_id', 'ix_promotion_tours_tour');
            $table->foreign('promotion_id', 'fk_promotion_tours_promotion')
                ->references('promotion_id')->on('promotions')->cascadeOnDelete();
            $table->foreign('tour_id', 'fk_promotion_tours_tour')
                ->references('tour_id')->on('tours')->cascadeOnDelete();
        });

        Schema::create('tour_reviews', function (Blueprint $table): void {
            $table->bigIncrements('review_id');
            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('rating');
            $table->text('content');
            $table->enum('status', ['pending', 'published', 'rejected'])->default('pending');
            $table->dateTime('created_at')->useCurrent();
            $table->unique(['tour_id', 'user_id'], 'uq_tour_review_user');
            $table->index('user_id', 'ix_tour_reviews_user');
            $table->index(['status', 'tour_id', 'rating'], 'ix_tour_reviews_rating');
            $table->foreign('tour_id', 'fk_tour_reviews_tour')->references('tour_id')->on('tours')->cascadeOnDelete();
            $table->foreign('user_id', 'fk_tour_reviews_user')->references('user_id')->on('users')->cascadeOnDelete();
        });

        DB::statement('ALTER TABLE promotions ADD CONSTRAINT ck_promotions_dates CHECK (ends_at >= starts_at)');
        DB::statement('ALTER TABLE tour_reviews ADD CONSTRAINT ck_tour_reviews_rating CHECK (rating BETWEEN 1 AND 5)');
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_reviews');
        Schema::dropIfExists('promotion_tours');
        Schema::dropIfExists('promotions');
    }
};

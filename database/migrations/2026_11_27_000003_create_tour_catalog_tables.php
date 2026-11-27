<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_categories', function (Blueprint $table): void {
            $table->smallIncrements('category_id');
            $table->string('name', 100)->unique('uq_category_name');
        });

        Schema::create('destinations', function (Blueprint $table): void {
            $table->increments('destination_id');
            $table->string('country', 100);
            $table->string('name', 150);
            $table->index(['country', 'name'], 'ix_destination_country_name');
        });

        Schema::create('tours', function (Blueprint $table): void {
            $table->bigIncrements('tour_id');
            $table->unsignedSmallInteger('category_id');
            $table->unsignedInteger('destination_id');
            $table->string('title', 200);
            $table->string('departure_city', 100);
            $table->date('departure_date');
            $table->unsignedSmallInteger('nights');
            $table->string('meal_plan', 80);
            $table->decimal('price', 12, 2);
            $table->string('image_url', 500);
            $table->text('description');
            $table->dateTime('archived_at')->nullable();
            $table->index(['category_id', 'archived_at', 'departure_date'], 'ix_tours_category_catalog');
            $table->index('destination_id', 'ix_tours_destination');
            $table->index(['archived_at', 'price'], 'ix_tours_catalog_price');
            $table->index('departure_date', 'ix_tours_departure');
            $table->foreign('category_id', 'fk_tours_category')->references('category_id')->on('tour_categories');
            $table->foreign('destination_id', 'fk_tours_destination')->references('destination_id')->on('destinations');
        });

        Schema::create('tour_guest_limits', function (Blueprint $table): void {
            $table->unsignedBigInteger('tour_id')->primary();
            $table->unsignedTinyInteger('max_adults');
            $table->unsignedTinyInteger('max_children');
            $table->foreign('tour_id', 'fk_guest_limits_tour')->references('tour_id')->on('tours')->cascadeOnDelete();
        });

        DB::statement('ALTER TABLE tours ADD CONSTRAINT ck_tours_nights CHECK (nights > 0), ADD CONSTRAINT ck_tours_price CHECK (price >= 0)');
        DB::statement('ALTER TABLE tour_guest_limits ADD CONSTRAINT ck_guest_limits_adults CHECK (max_adults > 0)');

        DB::table('tour_categories')->insert([
            ['name' => 'Горящие туры'],
            ['name' => 'Новогодние туры'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_guest_limits');
        Schema::dropIfExists('tours');
        Schema::dropIfExists('destinations');
        Schema::dropIfExists('tour_categories');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_documents', function (Blueprint $table): void {
            $table->bigIncrements('document_id');
            $table->unsignedBigInteger('user_id');
            $table->string('document_type', 80);
            $table->string('series', 30)->nullable();
            $table->string('number', 40);
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->index('user_id', 'ix_documents_user');
            $table->foreign('user_id', 'fk_documents_user')->references('user_id')->on('users')->cascadeOnDelete();
        });

        DB::statement('ALTER TABLE user_documents ADD CONSTRAINT ck_document_dates CHECK (expires_at >= issued_at)');
    }

    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};

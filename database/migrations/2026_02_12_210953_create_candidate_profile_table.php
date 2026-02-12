<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('candidate_profile', function (Blueprint $table) {
            $table->unsignedInteger('account_id')->primary();
            $table->string('first_name', 80);
            $table->string('last_name', 120);
            $table->string('phone', 30)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('photo_url', 500)->nullable();

            $table->unsignedSmallInteger('disability_type_id')->nullable();
            $table->unsignedTinyInteger('disability_degree')->nullable();
            $table->string('accessibility_needs', 255)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('disability_type_id')->references('id')->on('disability_type')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_profile');
    }
};

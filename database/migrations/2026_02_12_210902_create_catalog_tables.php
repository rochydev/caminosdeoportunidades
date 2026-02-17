<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disability_types', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 120)->unique();
        });

        Schema::create('job_categories', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 120)->unique();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 80)->unique();
        });

        Schema::create('contract_types', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 120)->unique();
        });

        Schema::create('workday_types', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 120)->unique();
        });

        Schema::create('modality_types', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 120)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modality_types');
        Schema::dropIfExists('workday_types');
        Schema::dropIfExists('contract_types');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('job_categories');
        Schema::dropIfExists('disability_types');
    }
};

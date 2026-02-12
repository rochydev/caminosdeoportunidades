<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disability_type', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 120)->unique();
        });

        Schema::create('job_category', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 120)->unique();
        });

        Schema::create('tag', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 80)->unique();
        });

        Schema::create('contract_type', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 120)->unique();
        });

        Schema::create('workday_type', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 120)->unique();
        });

        Schema::create('modality_type', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 120)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modality_type');
        Schema::dropIfExists('workday_type');
        Schema::dropIfExists('contract_type');
        Schema::dropIfExists('tag');
        Schema::dropIfExists('job_category');
        Schema::dropIfExists('disability_type');
    }
};

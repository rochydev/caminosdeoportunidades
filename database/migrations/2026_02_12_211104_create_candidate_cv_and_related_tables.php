<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('candidate_cvs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->string('title', 150)->default('Mi CV');
            $table->text('summary')->nullable();
            $table->text('skills')->nullable();
            $table->text('languages')->nullable();
            $table->enum('availability', ['FULL_TIME','PART_TIME','REMOTE_ONLY','HYBRID','FLEX'])->nullable();

            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('cv_experiences', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('cv_user_id');
            $table->string('company', 150);
            $table->string('position', 150);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();

            $table->index('cv_user_id', 'idx_exp_cv');
            $table->foreign('cv_user_id')->references('user_id')->on('candidate_cvs')->onDelete('cascade');
        });

        Schema::create('cv_educations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('cv_user_id');
            $table->string('institution', 180);
            $table->string('degree', 180);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();

            $table->index('cv_user_id', 'idx_edu_cv');
            $table->foreign('cv_user_id')->references('user_id')->on('candidate_cvs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_educations');
        Schema::dropIfExists('cv_experiences');
        Schema::dropIfExists('candidate_cvs');
    }
};

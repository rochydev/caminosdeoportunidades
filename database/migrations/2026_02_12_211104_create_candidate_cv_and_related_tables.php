<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('candidate_cv', function (Blueprint $table) {
            $table->unsignedInteger('account_id')->primary();
            $table->string('title', 150)->default('Mi CV');
            $table->text('summary')->nullable();
            $table->text('skills')->nullable();
            $table->text('languages')->nullable();
            $table->enum('availability', ['FULL_TIME','PART_TIME','REMOTE_ONLY','HYBRID','FLEX'])->nullable();

            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });

        Schema::create('cv_experience', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cv_account_id');
            $table->string('company', 150);
            $table->string('position', 150);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();

            $table->index('cv_account_id', 'idx_exp_cv');
            $table->foreign('cv_account_id')->references('account_id')->on('candidate_cv')->onDelete('cascade');
        });

        Schema::create('cv_education', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cv_account_id');
            $table->string('institution', 180);
            $table->string('degree', 180);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();

            $table->index('cv_account_id', 'idx_edu_cv');
            $table->foreign('cv_account_id')->references('account_id')->on('candidate_cv')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_education');
        Schema::dropIfExists('cv_experience');
        Schema::dropIfExists('candidate_cv');
    }
};

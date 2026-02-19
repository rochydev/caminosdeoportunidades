<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_offer', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_account_id');

            $table->unsignedSmallInteger('category_id')->nullable();
            $table->unsignedSmallInteger('contract_type_id')->nullable();
            $table->unsignedSmallInteger('workday_type_id')->nullable();
            $table->unsignedSmallInteger('modality_id')->nullable();

            $table->string('title', 255);
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('adaptations')->nullable();

            $table->string('city', 120)->nullable();
            $table->boolean('is_adapted')->default(false);

            $table->enum('status', ['DRAFT','PUBLISHED','CLOSED'])->default('DRAFT');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('company_account_id', 'idx_offer_company');
            $table->index('status', 'idx_offer_status');
            $table->index('city', 'idx_offer_city');
            $table->index(['category_id','contract_type_id','workday_type_id','modality_id'], 'idx_offer_filters');

            $table->foreign('company_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('job_category')->onDelete('set null');
            $table->foreign('contract_type_id')->references('id')->on('contract_type')->onDelete('set null');
            $table->foreign('workday_type_id')->references('id')->on('workday_type')->onDelete('set null');
            $table->foreign('modality_id')->references('id')->on('modality_type')->onDelete('set null');
        });

        Schema::create('offer_tag', function (Blueprint $table) {
            $table->unsignedInteger('offer_id');
            $table->unsignedSmallInteger('tag_id');
            $table->primary(['offer_id', 'tag_id']);
            $table->foreign('offer_id')->references('id')->on('job_offer')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tag')->onDelete('restrict');
        });

        Schema::create('offer_disability', function (Blueprint $table) {
            $table->unsignedInteger('offer_id');
            $table->unsignedSmallInteger('disability_type_id');
            $table->primary(['offer_id', 'disability_type_id']);
            $table->foreign('offer_id')->references('id')->on('job_offer')->onDelete('cascade');
            $table->foreign('disability_type_id')->references('id')->on('disability_type')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_disability');
        Schema::dropIfExists('offer_tag');
        Schema::dropIfExists('job_offer');
    }
};

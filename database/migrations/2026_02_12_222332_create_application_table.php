<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('application', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('offer_id');
            $table->unsignedInteger('candidate_account_id');

            $table->enum('status', ['SENT','IN_REVIEW','ACCEPTED','REJECTED','CANCELED'])->default('SENT');
            $table->text('company_notes')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['offer_id','candidate_account_id'], 'uk_offer_candidate');
            $table->index('offer_id', 'idx_app_offer');
            $table->index('candidate_account_id', 'idx_app_candidate');
            $table->index('status', 'idx_app_status');

            $table->foreign('offer_id')->references('id')->on('job_offer')->onDelete('cascade');
            $table->foreign('candidate_account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application');
    }
};

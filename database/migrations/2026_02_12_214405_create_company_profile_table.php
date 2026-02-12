<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_profile', function (Blueprint $table) {
            $table->unsignedInteger('account_id')->primary();
            $table->string('company_name', 255);
            $table->text('description')->nullable();
            $table->string('sector', 180)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('logo_url', 500)->nullable();

            $table->boolean('offers_adapted_positions')->default(false);
            $table->boolean('offers_remote_work')->default(false);
            $table->boolean('offers_reasonable_adjustments')->default(false);

            $table->enum('validation_status', ['PENDING','VALIDATED','REJECTED','BLOCKED'])->default('PENDING');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profile');
    }
};

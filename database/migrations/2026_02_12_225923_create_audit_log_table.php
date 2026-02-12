<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('actor_account_id')->nullable();
            $table->enum('entity_type', ['OFFER','APPLICATION','COMPANY','ACCOUNT']);
            $table->unsignedInteger('entity_id')->nullable();
            $table->string('action', 40);
            $table->string('details', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('actor_account_id', 'idx_log_actor');
            $table->index(['entity_type','entity_id'], 'idx_log_entity');
            $table->foreign('actor_account_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};

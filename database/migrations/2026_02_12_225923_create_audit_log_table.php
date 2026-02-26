<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->enum('entity_type', ['OFFER','JOB_APPLICATION','COMPANY','USER']);
            $table->unsignedInteger('entity_id')->nullable();
            $table->string('action', 40);
            $table->string('details', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('actor_user_id', 'idx_log_actor');
            $table->index(['entity_type','entity_id'], 'idx_log_entity');
            $table->foreign('actor_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

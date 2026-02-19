<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->string('type', 60);
            $table->string('title', 120);
            $table->string('body', 500)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index('account_id', 'idx_notif_account');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification');
    }
};

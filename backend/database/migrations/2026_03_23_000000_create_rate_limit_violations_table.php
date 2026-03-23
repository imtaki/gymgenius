<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rate_limit_violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->index();
            $table->string('endpoint')->index();
            $table->string('method', 10)->default('GET');
            $table->integer('limit')->comment('Rate limit threshold');
            $table->integer('current_count')->comment('Current request count when violated');
            $table->integer('window_in_minutes')->default(1)->comment('Rate limit window in minutes');
            $table->text('user_agent')->nullable();
            $table->string('identifier', 255)->index()->comment('User ID or IP identifier for rate limiting');
            $table->timestamps();

            // Foreign key constraint (optional, user might not exist yet)
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Composite index for common queries
            $table->index(['user_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['endpoint', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_limit_violations');
    }
};

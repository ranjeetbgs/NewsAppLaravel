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
        if (!Schema::hasTable('votes')) {
            Schema::create('votes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->default(0);
                $table->unsignedBigInteger('blog_id')->default(0);
                $table->unsignedBigInteger('option_id')->default(0);
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
                $table->foreign('option_id')->references('id')->on('blog_pool_options')->onDelete('cascade');
                // Adding indexes
                $table->index('created_at');
                $table->index('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
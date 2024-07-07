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
        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0);
            $table->dateTime('login_date_time')->nullable();
            $table->dateTime('logout_date_time')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Adding indexes
            $table->index('login_date_time');
            $table->index('logout_date_time');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logins');
    }
};

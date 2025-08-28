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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // zalo | facebook
            $table->string('external_id'); // id user trên Zalo/FB
            $table->unsignedBigInteger('user_id')->nullable(); // mapping user nội bộ
            $table->text('last_message')->nullable();
            $table->timestamp('last_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};

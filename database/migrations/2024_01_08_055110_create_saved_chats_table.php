<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_chats', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('messages');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_chats');
    }
}; 
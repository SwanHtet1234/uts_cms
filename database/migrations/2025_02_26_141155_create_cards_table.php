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
        Schema::create('cards', function (Blueprint $table) {
            $table->id()->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('cardNumber')->unique();
            $table->date('expireDate');
            $table->integer('cvv');
            $table->float('balance');
            $table->foreignId('type_scheme_id')->constrained('card_type_schemes')->onDelete('cascade');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};

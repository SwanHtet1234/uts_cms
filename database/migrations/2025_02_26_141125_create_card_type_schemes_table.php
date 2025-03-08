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
        Schema::create('card_type_schemes', function (Blueprint $table) {
            $table->id()->index();
            $table->foreignId('type_id')->constrained('card_types')->onDelete('cascade');
            $table->foreignId('scheme_id')->constrained('card_schemes')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_type_schemes');
    }
};

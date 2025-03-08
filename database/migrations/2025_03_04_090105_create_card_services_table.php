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
        Schema::create('card_services', function (Blueprint $table) {
            $table->id()->index();
            $table->foreignId('card_id')->constrained('cards')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->float('spendingLimit')->nullable();
            $table->enum('globalOrLocal', ['global', 'local']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_services');
    }
};

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
        Schema::create('fixed_terms', function (Blueprint $table) {
            $table->id();
            $table->double('amount')->notNull();
            $table->foreignId('account_id')->constrained();
            $table->double('interest')->notNull();
            $table->double('total')->notNull();
            $table->integer('duration')->notNull();
            $table->timestamps();
            $table->timestamp('closed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_terms');
    }
};

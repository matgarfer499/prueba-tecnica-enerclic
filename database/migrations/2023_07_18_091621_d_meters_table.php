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
        Schema::create('d_meters', function (Blueprint $table) {
            $table->id();
            $table->datetime('datatime');
            $table->enum('contador', ['contador1', 'contador2']);
            $table->float('power')->nullable();
            $table->float('energy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_meters');
    }
};

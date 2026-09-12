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
        Schema::dropIfExists('project_items_auto');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('project_items_auto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('qty', 15, 2);
            $table->string('unit', 50);
            $table->timestamps();
        });
    }
};

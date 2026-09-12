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
        Schema::table('project_files', function (Blueprint $table) {
            $table->enum('category', ['bq_excel', '2d', '3d', 'pdf', 'foto'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_files', function (Blueprint $table) {
            $table->enum('category', ['bq_excel', '2d', '3d', 'pdf'])->change();
        });
    }
};

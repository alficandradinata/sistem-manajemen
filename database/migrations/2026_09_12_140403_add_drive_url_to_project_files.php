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
            $table->string('drive_url', 2000)->nullable()->after('file_path');
            $table->string('file_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_files', function (Blueprint $table) {
            $table->dropColumn('drive_url');
            $table->string('file_path')->nullable(false)->change();
        });
    }
};

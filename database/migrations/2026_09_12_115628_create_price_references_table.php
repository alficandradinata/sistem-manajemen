<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });

        $rows = DB::table('project_items_price_only')
            ->join('projects', 'projects.id', '=', 'project_items_price_only.project_id')
            ->select(
                'projects.user_id',
                'project_items_price_only.name',
                'project_items_price_only.price',
                'project_items_price_only.created_at',
                'project_items_price_only.updated_at',
            )
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        if ($rows !== []) {
            DB::table('price_references')->insert($rows);
        }

        Schema::dropIfExists('project_items_price_only');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('project_items_price_only', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });

        Schema::dropIfExists('price_references');
    }
};

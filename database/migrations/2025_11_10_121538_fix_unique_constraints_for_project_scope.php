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
        // Fix services table
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['project_id', 'name']);
        });

        // Fix matches table
        Schema::table('matches', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['project_id', 'name']);
        });

        // Fix deltas table
        Schema::table('deltas', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['project_id', 'name']);
        });

        // Fix rules table
        Schema::table('rules', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['project_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse services table
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['project_id', 'name']);
            $table->unique(['name']);
        });

        // Reverse matches table
        Schema::table('matches', function (Blueprint $table) {
            $table->dropUnique(['project_id', 'name']);
            $table->unique(['name']);
        });

        // Reverse deltas table
        Schema::table('deltas', function (Blueprint $table) {
            $table->dropUnique(['project_id', 'name']);
            $table->unique(['name']);
        });

        // Reverse rules table
        Schema::table('rules', function (Blueprint $table) {
            $table->dropUnique(['project_id', 'name']);
            $table->unique(['name']);
        });
    }
};

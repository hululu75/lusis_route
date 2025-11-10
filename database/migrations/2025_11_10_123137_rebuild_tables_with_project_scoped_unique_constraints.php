<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * SQLite doesn't support dropping constraints directly.
     * We need to rebuild the tables with the correct constraints.
     */
    public function up(): void
    {
        // 1. Rebuild services table
        Schema::dropIfExists('services_new');
        Schema::create('services_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->string('name', 64);
            $table->enum('type', ['REQ', 'NOT', 'SAME', 'PUB', 'END'])->default('REQ');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('project_id');
            $table->unique(['project_id', 'name']); // Project-scoped unique
        });

        DB::statement('INSERT INTO services_new SELECT * FROM services');
        Schema::dropIfExists('services');
        Schema::rename('services_new', 'services');

        // 2. Rebuild matches table
        Schema::dropIfExists('matches_new');
        Schema::create('matches_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->string('name', 128);
            $table->enum('type', ['REQ', 'NOT', 'SAME', 'PUB', 'END'])->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('project_id');
            $table->unique(['project_id', 'name']); // Project-scoped unique
        });

        DB::statement('INSERT INTO matches_new SELECT * FROM matches');
        Schema::dropIfExists('matches');
        Schema::rename('matches_new', 'matches');

        // 3. Rebuild deltas table
        Schema::dropIfExists('deltas_new');
        Schema::create('deltas_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->string('name', 128);
            $table->string('next', 128)->nullable();
            $table->mediumText('definition')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('project_id');
            $table->unique(['project_id', 'name']); // Project-scoped unique
        });

        DB::statement('INSERT INTO deltas_new SELECT * FROM deltas');
        Schema::dropIfExists('deltas');
        Schema::rename('deltas_new', 'deltas');

        // 4. Rebuild rules table
        Schema::dropIfExists('rules_new');
        Schema::create('rules_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->string('name', 128);
            $table->string('class', 64);
            $table->enum('type', ['REQ', 'NOT', 'SAME', 'PUB', 'END'])->default('REQ');
            $table->foreignId('delta_id')->nullable()->constrained()->onDelete('set null');
            $table->string('on_failure', 128)->nullable();
            $table->string('matching_cond', 128)->nullable();
            $table->string('route_cond_ok', 128)->nullable();
            $table->string('route_cond_ko', 128)->nullable();
            $table->string('delta_next', 128)->nullable();
            $table->string('delta_cond_ok', 128)->nullable();
            $table->string('delta_cond_ko', 128)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('project_id');
            $table->unique(['project_id', 'name']); // Project-scoped unique
        });

        DB::statement('INSERT INTO rules_new SELECT * FROM rules');
        Schema::dropIfExists('rules');
        Schema::rename('rules_new', 'rules');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is destructive and should not be run in production
        // Reverting would require rebuilding tables with single-column unique constraints
    }
};

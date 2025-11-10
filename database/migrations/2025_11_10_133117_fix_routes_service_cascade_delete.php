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
     * Fix: Change routes.from_service_id foreign key from restrict to cascade.
     * This prevents 500 errors when deleting projects with routes.
     */
    public function up(): void
    {
        // For SQLite, we need to rebuild the table
        Schema::dropIfExists('routes_new');
        Schema::create('routes_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routefile_id')->constrained('route_files')->onDelete('cascade');
            $table->foreignId('from_service_id')->constrained('services')->onDelete('cascade');  // Changed from restrict
            $table->foreignId('match_id')->nullable()->constrained('matches')->onDelete('set null');
            $table->foreignId('rule_id')->nullable()->constrained('rules')->onDelete('set null');
            $table->string('chainclass', 128)->nullable();
            $table->enum('type', ['REQ', 'NOT', 'SAME', 'PUB', 'END'])->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        // Copy data
        DB::statement('INSERT INTO routes_new SELECT * FROM routes');

        // Replace table
        Schema::dropIfExists('routes');
        Schema::rename('routes_new', 'routes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rebuild with restrict
        Schema::dropIfExists('routes_new');
        Schema::create('routes_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routefile_id')->constrained('route_files')->onDelete('cascade');
            $table->foreignId('from_service_id')->constrained('services')->onDelete('restrict');
            $table->foreignId('match_id')->nullable()->constrained('matches')->onDelete('set null');
            $table->foreignId('rule_id')->nullable()->constrained('rules')->onDelete('set null');
            $table->string('chainclass', 128)->nullable();
            $table->enum('type', ['REQ', 'NOT', 'SAME', 'PUB', 'END'])->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        DB::statement('INSERT INTO routes_new SELECT * FROM routes');
        Schema::dropIfExists('routes');
        Schema::rename('routes_new', 'routes');
    }
};

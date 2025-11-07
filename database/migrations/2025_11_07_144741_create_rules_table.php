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
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)->unique();
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};

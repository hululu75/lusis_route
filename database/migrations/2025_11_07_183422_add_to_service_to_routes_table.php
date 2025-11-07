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
        Schema::table('routes', function (Blueprint $table) {
            // Add to_service_id after from_service_id
            $table->foreignId('to_service_id')->nullable()->after('from_service_id')->constrained('services')->onDelete('cascade');
            $table->index('to_service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropForeign(['to_service_id']);
            $table->dropIndex(['to_service_id']);
            $table->dropColumn('to_service_id');
        });
    }
};

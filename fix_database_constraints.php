<?php
/**
 * Direct database fix script for unique constraints
 * Run with: php fix_database_constraints.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting database constraint fix...\n\n";

try {
    DB::beginTransaction();

    // Get current columns for each table
    $tables = ['services', 'matches', 'deltas', 'rules'];

    foreach ($tables as $table) {
        echo "Processing table: $table\n";

        // Get current table structure
        $columns = Schema::getColumnListing($table);
        echo "  Current columns: " . implode(', ', $columns) . "\n";

        // Check if project_id exists
        if (!in_array('project_id', $columns)) {
            echo "  ERROR: project_id column missing!\n";
            continue;
        }

        // Get current indexes
        $indexes = DB::select("SELECT name, sql FROM sqlite_master WHERE type='index' AND tbl_name=?", [$table]);
        echo "  Current indexes:\n";
        foreach ($indexes as $index) {
            echo "    - {$index->name}\n";
        }

        // Drop old unique index if it exists
        $oldIndexName = $table . '_name_unique';
        try {
            DB::statement("DROP INDEX IF EXISTS {$oldIndexName}");
            echo "  Dropped old index: {$oldIndexName}\n";
        } catch (\Exception $e) {
            echo "  Could not drop index {$oldIndexName}: " . $e->getMessage() . "\n";
        }

        // Create new composite unique index
        $newIndexName = $table . '_project_id_name_unique';
        try {
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS {$newIndexName} ON {$table}(project_id, name)");
            echo "  Created new index: {$newIndexName}\n";
        } catch (\Exception $e) {
            echo "  Could not create index {$newIndexName}: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    // Clean up failed migration records
    echo "Cleaning up migration records...\n";
    DB::table('migrations')
        ->where('migration', '2025_11_10_123137_rebuild_tables_with_project_scoped_unique_constraints')
        ->delete();
    echo "  Removed failed migration record\n\n";

    DB::commit();

    echo "✓ Database constraint fix completed successfully!\n\n";
    echo "You can now test the project copy functionality.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

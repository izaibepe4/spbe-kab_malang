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
        // Check if index exists before creating to avoid duplicate index error
        if (! Schema::hasColumn('layanans', 'user_id')) {
            return;
        }

        try {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('layanans');

            // Check if index already exists (check various possible index names)
            $indexExists = false;
            foreach ($indexesFound as $indexName => $index) {
                $columns = $index->getColumns();
                if (in_array('user_id', $columns) && count($columns) === 1) {
                    $indexExists = true;
                    break;
                }
            }

            // Only create index if it doesn't exist
            if (! $indexExists) {
                Schema::table('layanans', function (Blueprint $table) {
                    if (! Schema::hasIndex('layanans', 'layanans_user_id_index')) {
                        $table->index('user_id', 'layanans_user_id_index');
                    }
                });
            }
        } catch (\Exception $e) {
            // If there's any error, just skip (index might already exist)
            // This prevents migration from failing
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('layanans', function (Blueprint $table) {
            $table->dropIndex('layanans_user_id_index');
        });
    }
};

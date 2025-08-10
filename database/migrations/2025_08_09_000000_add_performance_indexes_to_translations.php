<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add basic performance indexes for all databases
        Schema::table('translation_values', function (Blueprint $table) {
            // Add index for locale_id foreign key
            $table->index('locale_id', 'idx_translation_values_locale_id');
            
            // Add index for translation_key_id foreign key
            $table->index('translation_key_id', 'idx_translation_values_key_id');
            
            // Add composite index for the unique constraint (locale_id, translation_key_id)
            // This will significantly improve JOIN performance
            $table->index(['locale_id', 'translation_key_id'], 'idx_translation_values_locale_key');
            
            // Add reverse composite index for queries filtering by key first
            $table->index(['translation_key_id', 'locale_id'], 'idx_translation_values_key_locale');
            
            // Add unique constraint to prevent duplicate translations
            $table->unique(['translation_key_id', 'locale_id'], 'idx_translation_values_unique_key_locale');
        });

        Schema::table('translation_revisions', function (Blueprint $table) {
            // Add index for translation_value_id foreign key
            $table->index('translation_value_id', 'idx_translation_revisions_value_id');
            
            // Add index for user_id foreign key
            $table->index('user_id', 'idx_translation_revisions_user_id');
            
            // Add composite index for common query patterns
            $table->index(['translation_value_id', 'user_id'], 'idx_translation_revisions_value_user');
        });

        Schema::table('translation_keys', function (Blueprint $table) {
            // The 'key' field already has an index, but let's add a functional index for potential optimizations
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_keys_key_length ON translation_keys (LENGTH(key))');
        });

        Schema::table('translation_tags', function (Blueprint $table) {
            // The 'name' field already has an index, but let's add a functional index for case-insensitive searches
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_tags_name_lower ON translation_tags (LOWER(name))');
        });

        // Add PostgreSQL-specific optimizations if using PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // GIN indexes for full-text search
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_values_value_gin ON translation_values USING GIN (to_tsvector(\'english\', value))');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_keys_key_gin ON translation_keys USING GIN (to_tsvector(\'english\', key))');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_tags_name_gin ON translation_tags USING GIN (to_tsvector(\'english\', name))');
            
            // BRIN indexes for timestamps (efficient for large tables)
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_values_created_at_brin ON translation_values USING BRIN (created_at)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_values_updated_at_brin ON translation_values USING BRIN (updated_at)');
            
            // Partial indexes for non-null descriptions
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_keys_description_partial ON translation_keys (description) WHERE description IS NOT NULL');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_tags_description_partial ON translation_tags (description) WHERE description IS NOT NULL');
            
            // Pattern indexes for LIKE queries
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_values_value_pattern ON translation_values (value text_pattern_ops)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_keys_key_pattern ON translation_keys (key text_pattern_ops)');
            
            // Covering index for frequently accessed data
            DB::statement('CREATE INDEX IF NOT EXISTS idx_translation_values_covering ON translation_values (locale_id, translation_key_id) INCLUDE (value, created_at, updated_at)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translation_values', function (Blueprint $table) {
            $table->dropIndex('idx_translation_values_locale_id');
            $table->dropIndex('idx_translation_values_key_id');
            $table->dropIndex('idx_translation_values_locale_key');
            $table->dropIndex('idx_translation_values_key_locale');
            $table->dropUnique('idx_translation_values_unique_key_locale');
        });

        Schema::table('translation_revisions', function (Blueprint $table) {
            $table->dropIndex('idx_translation_revisions_value_id');
            $table->dropIndex('idx_translation_revisions_user_id');
            $table->dropIndex('idx_translation_revisions_value_user');
        });

        // Drop functional indexes
        DB::statement('DROP INDEX IF EXISTS idx_translation_keys_key_length');
        DB::statement('DROP INDEX IF EXISTS idx_translation_tags_name_lower');

        // Drop PostgreSQL-specific indexes if they exist
        if (DB::connection()->getDriverName() === 'pgsql') {
            $postgresqlIndexes = [
                'idx_translation_values_value_gin',
                'idx_translation_keys_key_gin',
                'idx_translation_tags_name_gin',
                'idx_translation_values_created_at_brin',
                'idx_translation_values_updated_at_brin',
                'idx_translation_keys_description_partial',
                'idx_translation_tags_description_partial',
                'idx_translation_values_value_pattern',
                'idx_translation_keys_key_pattern',
                'idx_translation_values_covering'
            ];
            
            foreach ($postgresqlIndexes as $index) {
                DB::statement("DROP INDEX IF EXISTS {$index}");
            }
        }
    }
}; 
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
        Schema::create('translation_key_translation_tag', function (Blueprint $table) {
            $table->foreignId('translation_key_id')
                ->constrained('translation_keys')
                ->cascadeOnDelete();
            $table->foreignId('translation_tag_id')
                ->constrained('translation_tags')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['translation_key_id', 'translation_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_key_translation_tag');
    }
};

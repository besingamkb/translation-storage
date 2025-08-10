<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Locale;
use App\Models\TranslationTag;

class LocaleAndTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined locales
        $locales = [
            ['code' => 'en_US', 'name' => 'English (United States)'],
            ['code' => 'fr_FR', 'name' => 'French (France)'],
            ['code' => 'es_ES', 'name' => 'Spanish (Spain)'],
        ];

        foreach ($locales as $locale) {
            Locale::firstOrCreate(
                ['code' => $locale['code']],
                ['name' => $locale['name']]
            );
        }

        // Create some sample translation tags
        $tags = ['UI', 'Email', 'Error Messages', 'Buttons', 'Notifications'];

        foreach ($tags as $tagName) {
            TranslationTag::firstOrCreate(['name' => $tagName]);
        }
    }
}

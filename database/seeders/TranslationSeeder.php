<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\TranslationTag;
use App\Models\TranslationValue;
use App\Models\TranslationRevision;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locales = Locale::all();
        $tags = TranslationTag::all();
        $faker = Faker::create();

        // Create 1000 translation keys
        for ($i = 0; $i < 50000; $i++) {
            $translationKey = TranslationKey::create([
                'key' => $faker->unique()->lexify('????.????'),
                'description' => $faker->sentence(),
            ]);

            // Attach a random count of tags (between 1 and all tags)
            if ($tags->count() > 0) {
                $randomTags = $tags->random(rand(1, $tags->count()));
                $translationKey->tags()->attach($randomTags);
            }

            // Create translation values for each locale
            foreach ($locales as $locale) {
                $translationValue = TranslationValue::create([
                    'translation_key_id' => $translationKey->id,
                    'locale_id' => $locale->id,
                    'value' => $faker->word(),
                ]);

                // Create 1-5 random revisions for each value
                $revisionCount = rand(1, 5);
                for ($r = 0; $r < $revisionCount; $r++) {
                    TranslationRevision::create([
                        'translation_value_id' => $translationValue->id,
                        'user_id' => 1,
                        'old' => $faker->word(),
                        'new' => $faker->word(),
                        'created_at' => now()->subDays(rand(0, 365)),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}

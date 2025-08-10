<?php

namespace Database\Factories;

use App\Models\TranslationValue;
use App\Models\Locale;
use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TranslationValue>
 */
class TranslationValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'translation_key_id' => TranslationKey::factory(),
            'locale_id' => Locale::factory(),
            'value' => $this->faker->sentence(),
        ];
    }
}

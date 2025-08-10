<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\TranslationValue;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TranslationRevision>
 */
class TranslationRevisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'translation_value_id' => TranslationValue::factory(),
            'old' => $this->faker->word(),
            'new' => $this->faker->word(),
            'user_id' => User::factory(), 
        ];
    }
}

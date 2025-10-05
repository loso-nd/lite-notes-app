<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'title' => fake()->sentence(),
            'text' => fake()->paragraphs(3, true),
            'user_id' => \App\Models\User::factory(),
            'notebook_id' => \App\Models\NoteBook::factory(),
        ];
    }
}

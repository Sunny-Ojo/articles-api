<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(5, true),
            'author' => $this->faker->name,
            'published_at' =>  $this->faker->boolean(70) ? now() : null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn() => [
            'published_at' => now(),
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn() => [
            'published_at' => null,
        ]);
    }
}

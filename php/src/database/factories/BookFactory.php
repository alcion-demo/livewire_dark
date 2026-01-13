<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\BookCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence(3), // 3語程度のダミータイトル
            'category'    => $this->faker->randomElement(BookCategory::cases()), // Enumからランダム
            'price'       => $this->faker->numberBetween(500, 5000), // 500円〜5000円
            'description' => $this->faker->realText(100), // 100文字程度の説明文
            'url'         => $this->faker->url(),
            'image'       => null, // テストでは一旦画像なしでOK
        ];
    }
}

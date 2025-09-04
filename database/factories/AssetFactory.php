<?php

namespace Database\Factories;

use App\Enums\AssetTypeEnum;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'name' => ucfirst($name),
            'symbol' => strtoupper(substr($name, 0, 3)),
            'type' => $this->faker->randomElement(AssetTypeEnum::cases()),
            'decimals' => $this->faker->numberBetween(1, 18),
            'logo' => $this->faker->image(),
            'description' => $this->faker->paragraph(),
            'website' => $this->faker->url(),
            'twitter' => $this->faker->url(),
            'discord' => $this->faker->url(),
            'telegram' => $this->faker->url(),
        ];
    }
}

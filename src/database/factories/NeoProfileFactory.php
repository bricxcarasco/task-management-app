<?php

namespace Database\Factories;

use App\Enums\PrefectureTypes;
use App\Models\NeoProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class NeoProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NeoProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'neo_id' => $this->faker->randomNumber(),
            'nationality' => $this->faker->word(),
            'prefecture' => PrefectureTypes::getRandomValue(),
            'city' => $this->faker->city(),
            'address' => $this->faker->address(),
            'building' => $this->faker->buildingNumber(),
        ];
    }
}

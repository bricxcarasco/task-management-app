<?php

namespace Database\Factories;

use App\Models\ExpertAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExpertAttributeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExpertAttribute::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('ja_JP');

        $code = $faker->unique()->bothify('?###??##');

        return [
            'attribute_code' => Str::upper(substr($code, 0, 3)."-".substr($code, 3)),
            'attribute_name' => Str::title($faker->jobTitle()),
            'is_searchable' => $faker->unique()->numberBetween(0, 1),
        ];
    }
}

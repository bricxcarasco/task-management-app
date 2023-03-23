<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\BusinessCategory;

class BusinessCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BusinessCategory::class;

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
            'business_category_code' => Str::upper(substr($code, 0, 3)."-".substr($code, 3)),
            'business_category_name' => Str::title($faker->jobTitle()),
            'business_category_example' => $faker->sentence(10),
        ];
    }
}

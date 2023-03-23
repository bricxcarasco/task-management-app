<?php

namespace Database\Factories;

use App\Enums\Neo\OrganizationAttributeType;
use App\Models\Neo;
use Illuminate\Database\Eloquent\Factories\Factory;

class NeoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Neo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'organization_name' => $this->faker->sentence(2),
            'organization_kana' => $this->faker->kanaName(),
            'organization_type' => OrganizationAttributeType::getRandomValue(),
            'establishment_date' => $this->faker->date(),
            'email' => $this->faker->email(),
            'tel' => $this->faker->phoneNumber(),
            'site_url' => $this->faker->url(),
        ];
    }
}

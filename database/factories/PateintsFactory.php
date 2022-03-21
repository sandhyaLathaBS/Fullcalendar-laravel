<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PateintsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'disease' => $this->faker->title(),
            'diagnosis' => $this->faker->paragraph(),
            'is_admitted' => 1,
            'is_active' => 1
        ];
    }
}
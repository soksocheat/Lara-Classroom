<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LecturerFactory extends Factory
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
            'birthdate' => $this->faker->date(),
            'place_of_birth' => $this->faker->address(),
            'address' => $this->faker->address(),
            'email' => $this->faker->unique()->email(),
            'phone' => $this->faker->unique()->phoneNumber()
        ];
    }
}

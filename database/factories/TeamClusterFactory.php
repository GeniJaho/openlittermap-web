<?php

namespace Database\Factories;

use App\Models\Teams\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamClusterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'team_id' => Team::factory(),
            'data' => [],
            'zoom' => $this->faker->randomElement(range(2, 16)),
            'created_at' => $this->faker->date()
        ];
    }
}

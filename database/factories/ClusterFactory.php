<?php

namespace Database\Factories;

use App\Models\Cluster;
use App\Models\Teams\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClusterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cluster::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $pointCount = $this->faker->randomDigitNotNull;
        return [
            'lat' => $this->faker->latitude,
            'lon' => $this->faker->longitude,
            'point_count' => $pointCount * 1000,
            'point_count_abbreviated' => "{$pointCount}k",
            'geohash' => 'gcpvn219nm0ughyj8uemwkpb',
            'zoom' => $this->faker->randomElement(range(6, 18))
        ];
    }

    /**
     * Indicate that the cluster belongs to a team.
     *
     * @param Team|null $team
     * @return Factory
     */
    public function forTeam(Team $team = null): Factory
    {
        $team = $team ?? Team::factory()->create();

        return $this->state(function (array $attributes) use ($team) {
            return [
                'clusterable_id' => $team->id,
                'clusterable_type' => Team::class
            ];
        });
    }
}

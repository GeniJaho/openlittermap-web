<?php

namespace Tests\Unit\Models;

use App\Models\Cluster;
use App\Models\Teams\Team;
use Tests\TestCase;

class ClusterTest extends TestCase
{
    public function test_it_has_a_clusterable_relationship()
    {
        /** @var Team $team */
        $team = Team::factory()->create();
        $cluster = Cluster::factory()->forTeam($team)->create();

        $this->assertInstanceOf(Team::class, $cluster->clusterable);
        $this->assertTrue($team->is($cluster->clusterable));
    }

    public function test_clusterable_can_be_null()
    {
        $cluster = Cluster::factory()->create();

        $this->assertNull($cluster->clusterable);
    }
}

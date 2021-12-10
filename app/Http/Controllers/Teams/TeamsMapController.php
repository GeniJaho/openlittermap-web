<?php

namespace App\Http\Controllers\Teams;

use App\Models\TeamCluster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamsMapController extends Controller
{
    /**
     * Get clusters for the teams map
     *
     * @return array
     */
    public function index(Request $request)
    {
        return TeamCluster::query()
            ->where([
                'team_id' => $request->team_id,
                'zoom' => $request->zoom,
                'created_at' => now()->toDateString()
            ])
            ->first()
            ->data;
    }
}

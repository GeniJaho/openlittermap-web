<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;

class TeamsDataController extends Controller
{
    /**
     * Get the combined effort for 1 or all of the users teams for the time-period
     *
     * @return array
     */
    public function index ()
    {
        $teamIds = $this->getTeamIds();

        // period
        if (request()->period === 'today') $period = now()->startOfDay();
        else if (request()->period === 'week') $period = now()->startOfWeek();
        else if (request()->period === 'month') $period = now()->startOfMonth();
        else if (request()->period === 'year') $period = now()->startOfYear();
        else if (request()->period === 'all') $period = '2020-11-22 00:00:00'; // date of writing

        $query = Photo::query()
            ->whereIn('team_id', $teamIds)
            ->whereDate('created_at', '>=', $period);

        $photosCount = $query->count();

        $membersCount = $query->distinct()->count('user_id');

        // might need photo.verified_at
        $litterCount = Photo::whereIn('team_id', $teamIds)
            ->whereDate('updated_at', '>=', $period)
            ->where('verified', 2)
            ->sum('total_litter');

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => []
        ];

        $photos = $query->take(1000)->get();

        // Populate geojson object
        foreach ($photos as $photo)
        {
            $feature = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$photo->lon, $photo->lat]
                ],

                'properties' => [
                    'photo_id' => $photo->id,
                    'img' => $photo->filename,
                    'model' => $photo->model,
                    'datetime' => $photo->datetime,
                    'latlng' => [$photo->lat, $photo->lon],
                    'text' => $photo->result_string
                ]
            ];

            array_push($geojson["features"], $feature);
        }

        return [
            'photos_count' => $photosCount,
            'litter_count' => $litterCount,
            'members_count' => $membersCount,
            'geojson' => array_slice($geojson, 0, 4)
        ];
    }

    /**
     * Returns the team ids depending on user request
     * If 0, we want to use all teamIds
     * If it's not 0, we only want the data for this team
     */
    protected function getTeamIds(): array
    {
        $teamIds = Auth::user()->teams->pluck('id')->toArray();

        if (request()->team_id && in_array(request()->team_id, $teamIds))
        {
            $teamIds = [request()->team_id];
        }

        return $teamIds;
    }
}

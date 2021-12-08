<?php

namespace App\Console\Commands;

use App\Models\Photo;
use App\Models\TeamCluster;
use App\Models\Teams\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandAlias;

class GenerateTeamClusters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clusters:generate-team-clusters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all clusters for teams photos';

    private $clustersDir = 'team-clusters.json';
    private $featuresDir = 'team-features.json';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start = microtime(true);

        $teamsWithImages = Team::where('total_images', '>', 0)->get();

        foreach ($teamsWithImages as $team) {
            TeamCluster::where([
                'team_id' => $team->id,
                'created_at' => now()->toDateString()
            ])->delete();

            $photos = Photo::query()
                ->select('lat', 'lon')
                ->where(['team_id' => $team->id])
                ->cursor();

            $features = $photos->map(function ($photo) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$photo->lon, $photo->lat]
                    ]
                ];
            });

            $features = json_encode($features, JSON_NUMERIC_CHECK);
            Storage::put("/data/$this->featuresDir", $features);

            $this->createClusters($team);
        }

        $finish = microtime(true);
        $this->info("Total Time: " . ($finish - $start));

        return CommandAlias::SUCCESS;
    }

    protected function createClusters(Team $team): void
    {
        $rootDir = config('app.root_dir');
        $zoomLevels = range(2, 16);

        $this->info("Team $team->name:");

        foreach ($zoomLevels as $zoomLevel) {
            $this->line("Zoom level $zoomLevel");

            exec("node app/Node/supercluster-php $rootDir $zoomLevel $this->featuresDir $this->clustersDir");

            $clusters = json_decode(Storage::get("/data/$this->clustersDir"));

            $clusters = array_filter($clusters, function ($cluster) {
                return isset($cluster->properties);
            });

            $data = array_map(function ($cluster) {
                return [
                    'lat' => $cluster->geometry->coordinates[1],
                    'lon' => $cluster->geometry->coordinates[0],
                    'point_count' => $cluster->properties->point_count,
                    'point_count_abbreviated' => $cluster->properties->point_count_abbreviated,
                    'geohash' => \GeoHash::encode($cluster->geometry->coordinates[1], $cluster->geometry->coordinates[0]),
                ];
            }, $clusters);

            TeamCluster::create([
                'team_id' => $team->id,
                'data' => $data,
                'zoom' => $zoomLevel,
                'created_at' => now()
            ]);
        }
    }
}

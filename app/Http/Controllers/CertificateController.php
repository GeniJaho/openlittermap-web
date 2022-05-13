<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Photo;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function show(Certificate $certificate)
    {
        $certificate['geojson'] = $this->getPhotosFromCertificate($certificate);

        return $certificate;
    }

    private function getPhotosFromCertificate(Certificate $certificate)
    {
        $photos = Photo::query()
            ->whereBetween('id', [$certificate->from, $certificate->to])
            ->with([
                'user:id,name,username,show_username_maps,show_name_maps,settings',
                'user.team:is_trusted',
                'team:id,name',
                'customTags:photo_id,tag',
            ])
            ->get();

        // Populate geojson object
        $features = [];
        foreach ($photos as $photo) {
            $name = $photo->user->show_name_maps ? $photo->user->name : null;
            $username = $photo->user->show_username_maps ? $photo->user->username : null;
            $team = $photo->team ? $photo->team->name : null;
            $filename = ($photo->user->is_trusted || $photo->verified >= 2) ? $photo->filename : '/assets/images/waiting.png';
            $resultString = $photo->verified >= 2 ? $photo->result_string : null;

            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$photo->lat, $photo->lon]
                ],
                'properties' => [
                    'photo_id' => $photo->id,
                    'result_string' => $resultString,
                    'filename' => $filename,
                    'datetime' => $photo->datetime,
                    'time' => $photo->datetime,
                    'cluster' => false,
                    'verified' => $photo->verified,
                    'name' => $name,
                    'username' => $username,
                    'team' => $team,
                    'picked_up' => $photo->picked_up,
                    'social' => $photo->user->social_links,
                    'custom_tags' => $photo->customTags->pluck('tag')
                ]
            ];
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }
}

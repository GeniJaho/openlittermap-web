<?php

namespace App\Http\Controllers;

use App\Exports\CreateCSVExport;
use App\Jobs\EmailUserExportCompleted;

use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Location\City;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadControllerNew extends Controller
{
    /**
     * Download data for a location
     *
     * @location_type $string = 'country', 'state', or 'city'
     * @country = 'Ireland'
     * @state = 'County Cork'
     * @city = 'Cork'
     */
    public function index (Request $request)
    {
        $email = (is_null(auth()->user()))
            ? $request->email
            : auth()->user()->email;

        $x     = new \DateTime();
        $date  = $x->format('Y-m-d');
        $date  = explode('-', $date);
        $year  = $date[0];
        $month = $date[1];
        $day   = $date[2];
        $unix  = now()->timestamp;

        $path = $year.'/'.$month.'/'.$day.'/'.$unix.'/';  // 2020/10/25/unix/
        $location_id = 0;

        try
        {
            if ($request->type === 'city')
            {
                if ($city = City::find($request->locationId))
                {
                    $path .= $city->city . '_OpenLitterMap.csv';
                    $location_id = $city->id;
                }
            }
            else if ($request->type === 'state')
            {
                if ($state = State::find($request->locationId))
                {
                    $path .= $state->state . '_OpenLitterMap.csv';
                    $location_id = $state->id;
                }
            }
            else if ($request->type === 'country')
            {
                if ($country = Country::find($request->locationId))
                {
                    $path .= $country->country . '_OpenLitterMap.csv';
                    $location_id = $country->id;
                }
            }
            else
            {
                $path = $year.'/'.$month.'/'.$day.'/' . 'Global_OpenLitterMap.csv';
            }

            $this->export($request->type, $location_id, $path, $email);

            return ['success' => true];
        }

        catch (Exception $e)
        {
            Log::info(['download failed', $e->getMessage()]);

            return ['success' => false];
        }
    }

    /**
     * @param string $type
     * @param int $locationId
     * @param string $path
     * @param string $email
     */
    protected function export(string $type, int $locationId, string $path, string $email): void
    {
        $disk = 's3';

        if (Storage::disk($disk)->exists($path)) {
            $this->dispatch(new EmailUserExportCompleted($email, $path));
            return;
        }

        /* Dispatch job to create CSV file for export */
        (new CreateCSVExport($type, $locationId))
            ->queue($path, $disk, null, ['visibility' => 'public'])
            ->chain([
                new EmailUserExportCompleted($email, $path)
            ]);
    }
}

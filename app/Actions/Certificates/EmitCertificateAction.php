<?php

namespace App\Actions\Certificates;

use App\Models\Certificate;

class EmitCertificateAction
{
    public function run(): Certificate
    {
        $lastCertificate = Certificate::latest()->first();
        $from = $lastCertificate ? $lastCertificate->to + 1 : 1;
        $to = $from + Certificate::ITEMS_COUNT;

        return Certificate::create([
            'from' => $from,
            'to' => $to
        ]);
    }
}

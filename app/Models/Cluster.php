<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Cluster extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the parent clusterable model (user or team).
     */
    public function clusterable(): MorphTo
    {
        return $this->morphTo();
    }
}

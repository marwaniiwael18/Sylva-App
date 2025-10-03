<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventUser extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected $table = 'event_user';

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'registered_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;
}
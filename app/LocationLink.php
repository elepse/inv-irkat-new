<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LocationLink
 *
 * @property-read \App\Location $location
 * @mixin \Eloquent
 */
class LocationLink extends Model
{
     public function location() {
    return $this->belongsTo(Location::class, 'location_id');
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Patoughi\Common\Orm\Models\Station as StationModel;

class Station extends StationModel
{
    public function loadingLocations(): HasMany
    {
        return $this->hasMany(LoadingLocation::class);
    }
}

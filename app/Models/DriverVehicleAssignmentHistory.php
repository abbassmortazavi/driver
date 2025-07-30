<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Patoughi\Common\Orm\Models\DriverVehicleAssignmentHistory as DriverVehicleAssignmentHistoryModel;

class DriverVehicleAssignmentHistory extends DriverVehicleAssignmentHistoryModel
{
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }
}

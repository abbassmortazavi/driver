<?php

namespace App\Repository\Driver;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class DriverRepository extends BaseRepository implements DriverRepositoryInterface
{
    /**
     * @param  Driver  $model
     */
    public function __construct(Driver $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  Driver  $driver
     * @param  array  $attributes
     * @return Collection
     */
    public function driverListVehicles(Driver $driver, array $attributes): Collection
    {
        return $driver->vehicles()
            ->when(isset($attributes['status']), fn ($q) => $q->where('status', $attributes['status']))->get();
    }
}

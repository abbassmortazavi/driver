<?php

namespace App\Repository\Version;

use App\Models\Version;
use Patoughi\Common\Enums\VersionStatusEnum;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class VersionRepository extends BaseRepository implements VersionRepositoryInterface
{
    /**
     * @param Version $model
     */
    public function __construct(Version $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $platform
     * @param string $type
     * @return null|object
     */
    public function getLatestVersion(string $platform, string $type): ?object
    {
        return $this->model->query()->where('platform', $platform)
            ->where('type', $type)
            ->where('status', VersionStatusEnum::RELEASED)
            ->latest()->first();
    }
}

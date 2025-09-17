<?php

namespace App\Repository\Version;

interface VersionRepositoryInterface
{
    /**
     * @param  string  $platform
     * @param  string  $type
     * @return mixed
     */
    public function getLatestVersion(string $platform, string $type): mixed;
}

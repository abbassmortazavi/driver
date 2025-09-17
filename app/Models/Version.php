<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Patoughi\Common\Orm\Models\Version as VersionModel;

class Version extends VersionModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected array $auditInclude = [
        'version',
    ];
}

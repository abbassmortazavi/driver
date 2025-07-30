<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Patoughi\Common\Orm\Models\KycLog as KycLogModel;

class KycLog extends KycLogModel
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(PublicUser::class);
    }
}

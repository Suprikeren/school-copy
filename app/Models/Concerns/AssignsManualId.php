<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait AssignsManualId
{
    public function initializeAssignsManualId(): void
    {
        $this->incrementing = false;
    }

    protected static function bootAssignsManualId(): void
    {
        static::creating(function (Model $model) {
            if (! $model->getKey()) {
                $model->setAttribute(
                    $model->getKeyName(),
                    ((int) DB::table($model->getTable())->max($model->getKeyName())) + 1
                );
            }
        });
    }
}

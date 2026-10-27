<?php

namespace Database\Seeders\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait AssignsExplicitIds
{
    protected function nextId(string $table): int
    {
        return ((int) DB::table($table)->max('id')) + 1;
    }

    protected function firstOrCreateWithId(
        string $modelClass,
        array $unique,
        array $attributes = [],
        bool $withTrashed = false
    ): Model {
        $query = $modelClass::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        $existing = $query->where($unique)->first();

        if ($existing !== null) {
            return $existing;
        }

        return $modelClass::query()->forceCreate(array_merge(
            $unique,
            $attributes,
            ['id' => $this->nextId((new $modelClass)->getTable())]
        ));
    }
}

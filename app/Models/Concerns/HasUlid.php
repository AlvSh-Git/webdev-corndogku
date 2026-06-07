<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Gives a model a stable, cross-database `ulid` identity used by the sync engine.
 *
 * A ULID is assigned automatically on create (if one was not already supplied —
 * e.g. when applying a row that originated on the peer database, its existing
 * ulid is kept). The column is guarded from mass assignment on purpose: it is
 * identity, not user-editable data.
 */
trait HasUlid
{
    public static function bootHasUlid(): void
    {
        static::creating(function ($model) {
            if (empty($model->ulid)) {
                $model->ulid = (string) Str::ulid();
            }
        });
    }
}

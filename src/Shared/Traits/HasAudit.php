<?php

namespace Purdia\Shared\Traits;

/**
 * Trait for models that need audit columns.
 * Automatically sets created_by, updated_by, deleted_by via observer.
 *
 * Usage: add `use HasAudit;` to your model.
 * Make sure the model's migration has: created_by, updated_by, deleted_by columns.
 */
trait HasAudit
{
    public static function bootHasAudit(): void
    {
        static::creating(function ($model) {
            if (! $model->created_by && auth()->check()) {
                $model->created_by = (string) auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = (string) auth()->id();
            }
        });

        if (method_exists(static::class, 'deleting')) {
            static::deleting(function ($model) {
                if (auth()->check() && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($model))) {
                    $model->deleted_by = (string) auth()->id();
                    $model->saveQuietly();
                }
            });
        }
    }
}

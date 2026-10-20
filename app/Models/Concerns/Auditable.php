<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;

trait Auditable
{
    /** Kolom yang tidak pernah dicatat nilainya. */
    protected static array $auditHidden = ['password', 'remember_token'];

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditLog::record('created', static::class, $model->getKey(), null, $model->auditFilter($model->getAttributes()));
        });

        static::updated(function ($model) {
            $raw = $model->getChanges();
            if (array_key_exists('password', $raw)) {
                AuditLog::record('password_changed', static::class, $model->getKey());
            }

            $changes = $model->auditFilter($raw);
            if (!$changes) {
                return;
            }
            $old = array_intersect_key($model->auditFilter($model->getRawOriginal()), $changes);
            AuditLog::record('updated', static::class, $model->getKey(), $old, $changes);
        });

        static::deleted(function ($model) {
            AuditLog::record('deleted', static::class, $model->getKey(), $model->auditFilter($model->getRawOriginal()));
        });
    }

    protected function auditFilter(array $values): array
    {
        $values = array_diff_key($values, array_flip(static::$auditHidden));
        unset($values['updated_at'], $values['created_at']);
        return $values;
    }
}

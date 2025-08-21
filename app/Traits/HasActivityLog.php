<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;

trait HasActivityLog
{
    protected static string $logName = '';

    public function getActivitylogOptions(): LogOptions
    {
        $attributes = $this->fillable ?? [];

        return LogOptions::defaults()->logOnly($attributes)->useLogName(static::$logName ?: class_basename($this))->logOnlyDirty()->dontSubmitEmptyLogs();
    }
}

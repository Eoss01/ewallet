<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

trait HasAuditFields
{
    protected static function bootHasAuditFields()
    {
        static::creating(function ($model)
        {
            do
            {
                $model->cid = Str::random(10);
            } while ($model->newQuery()->where('cid', $model->cid)->exists());

            if (Auth::check())
            {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model)
        {
            if (Auth::check())
            {
                $model->updated_by = Auth::id();
                $model->version = $model->version + 1;
            }
        });

        static::deleting(function ($model)
        {
            if (Auth::check())
            {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }
}

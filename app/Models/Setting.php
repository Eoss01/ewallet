<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasAuditFields;
use App\Traits\HasActivityLog;

class Setting extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait, LogsActivity, HasAuditFields, HasActivityLog;

    protected $softCascade = [];

    protected $table = 'settings';

     protected $fillable = [
        'cid',
        'rebate_percent',
        'created_by',
        'updated_by',
        'deleted_by',
        'version',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasAuditFields;
use App\Traits\HasActivityLog;

class Wallet extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait, LogsActivity, HasAuditFields, HasActivityLog;

    protected $softCascade = ['transactions'];

    protected $table = 'wallets';

    protected $fillable = [
        'cid',
        'user_id',
        'wallet_balance',
        'created_by',
        'updated_by',
        'deleted_by',
        'version',
    ];

    public function getRouteKeyName()
    {
        return 'cid';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'wallet_id', 'id');
    }
}

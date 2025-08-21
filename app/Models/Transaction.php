<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasAuditFields;
use App\Traits\HasActivityLog;

class Transaction extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait, LogsActivity, HasAuditFields, HasActivityLog;

    protected $softCascade = [];

    protected $table = 'transactions';

    protected $fillable = [
        'cid',
        'wallet_id',
        'transaction_type',
        'transaction_date',
        'transaction_amount',
        'created_by',
        'updated_by',
        'deleted_by',
        'version',
    ];

    protected $casts = [
        'transaction_type' => TransactionType::class,
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'id');
    }
}

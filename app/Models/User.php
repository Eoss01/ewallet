<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\HasAuditFields;
use App\Traits\HasActivityLog;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable, HasRoles, SoftDeletes, SoftCascadeTrait, LogsActivity, HasAuditFields, HasActivityLog;

    protected $softCascade = ['wallet'];

    protected $table = 'users';

    protected $fillable = [
        'cid',
        'uid',
        'name',
        'phone',
        'email',
        'password',
        'photo',
        'join_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'version',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'status' => ActiveStatus::class,
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }
}

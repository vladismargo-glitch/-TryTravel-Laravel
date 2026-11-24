<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public const CUSTOMER_ROLE = 1;
    public const ADMIN_ROLE = 2;

    protected $primaryKey = 'user_id';

    protected $authPasswordName = 'password_hash';

    public $timestamps = false;

    protected $fillable = [
        'role_id', 'last_name', 'first_name', 'email', 'phone', 'password_hash', 'consent_at',
    ];

    protected $hidden = ['password_hash'];
}

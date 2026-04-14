<?php

namespace Aliziodev\LaravelKaryawanCore\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];
}

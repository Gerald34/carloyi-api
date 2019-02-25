<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthorizedModel extends Model
{
    public $table = "authorizedAccounts";
    public $fillable = [
        'id',
        'email',
        'password',
        'name',
        'last_name',
        'authState',
        'created_at',
        'updated_at'
    ];
}


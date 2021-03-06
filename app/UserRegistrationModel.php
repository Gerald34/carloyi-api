<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Class UserRegistrationModel
 * @package App
 */
class UserRegistrationModel extends Model
{
    //
    protected $primaryKey = 'id';
    protected $table = 'vfq0g_users';
    public $timestamps = false;
    protected $fillable = array(
        'username',
        'password',
        'name',
        'lastName',
        'email',
        'lastvisitDate',
        'registerDate',
        'lastResetTime',
        'faxNumber' => '',
        'physicalAddress' => '',
        'postalAddress' => '',
        'params' => '',
        'api_token',
        'otp',
        'activation',
        'identity',
        'nationality',
        'updateDateTime',
        'gender',
        'agreement'
    );
    
}

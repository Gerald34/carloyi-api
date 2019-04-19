<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarnetUsers extends Model
{
    //
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
    'contactNumber',
    'faxNumber',
    'birth_date',
    'physicalAddress',
    'street_number',
    'street_name',
    'suburb',
    'city',
    'province',
    'country',
    'lastvisitDate',
    'nationality',
    'identity',
    'agreement' => '',
    'gender',
    'updateDateTime'

);
}

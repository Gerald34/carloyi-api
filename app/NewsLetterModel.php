<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsLetterModel extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'vfq0g_newsletter';
    public $timestamps = false;
    protected $fillable = array(
        'name',
        'email'
    );
}

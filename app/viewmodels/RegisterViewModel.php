<?php

namespace App\viewmodels;

use Illuminate\Database\Eloquent\Model;

class RegisterViewModel extends Model
{
    //
    protected $table ="vfq0g_users";

    const CREATED_AT = 'registerDate';
    const UPDATED_AT = 'lastvisitDate';



    public $id;
    public $username;
    public $name;
    public $lastName;
    public $email;
    public $password;
    public $api_token;
    //public $lastresetTime;

    protected $fillable = ['name','username','lastName','email','password','api_token'];


    public function register() {




        if($this->userExists())
        {
            return ['code' => -1, 'error' =>'user exists', 'data' => [] ];
        }
        $this->password = md5($this->password);
        //$this->lastresetTime =  '0000-00-00 00:00:00';

        $this->fill($this->getNewUserValues());

        //$user = self::create($this->getNewUserValues());

        $saved = $this->save();
        if($saved)
        {
            return ['code' => 1, 'error' =>'', 'data' => [$this] ];
        }

        return ['code' => -1, 'error' =>'failed to save', 'data' => [$this] ];


    }

    private function userExists()
    {
        //$user = RegisterViewModel::where('email', $this->email)->first()->toSql();
        $user = self::where(['email' => $this->email])->first();
        return ($user == null)? FALSE : TRUE;
    }



        private function getNewUserValues()
    {
        return
        [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'username' => $this->email,
            'lastName' => $this->lastName,
            'api_token' => str_random(60)
        ];
    }

}

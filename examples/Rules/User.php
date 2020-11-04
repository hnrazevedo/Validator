<?php

namespace HnrAzevedo\Validator\Example\Rules;

use HnrAzevedo\Validator\Validator;
use HnrAzevedo\Validator\Rules;

Class User{

    public function __construct()
    {
        Validator::add($this, function(Rules $rules){
            $rules->action('login')
                  ->field('email',[
                      'minlength' => 1,
                      'filter' => FILTER_VALIDATE_EMAIL,
                      'regex' => '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',
                      'required' => true
                  ], 'Email address')
                  ->field('password',['minlength'=>6,'maxlength'=>20,'required'=>true])
                  ->field('password2',['minlength'=>6,'maxlength'=>20,'equals'=>'password','required'=>true])
                  ->field('remember',['minlength'=>2,'maxlength'=>2,'required'=>false])
                  ->field('birth',['type'=>'date','required'=>true])
                  ->field('phones',['mincount'=>2,'maxcount'=>3,'required'=>true,'minlength'=>8,'maxlength'=>9]);

			return $rules;
        });

        return $this;
    }

}

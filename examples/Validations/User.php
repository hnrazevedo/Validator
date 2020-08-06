<?php

namespace HnrAzevedo\Validator;

Class User{

    public function __construct()
    {

        Validator::add($this, function(Rules $rules){
            $rules->setAction('login')
                  ->addField('email',['minlength'=>1,'regex'=>'/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/','required'=>true])
                  ->addField('password',['minlength'=>6,'maxlength'=>20,'required'=>true])
                  ->addField('password2',['equals'=>'password','required'=>true])
                  ->addField('remember',['minlength'=>2,'maxlength'=>2,'required'=>false])
                  ->addField('phones',['mincount'=>2,'maxcount'=>3,'required'=>true,'minlength'=>8,'maxlength'=>9]);

			return $rules;
        });

        return $this;
    }

}

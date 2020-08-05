<?php

namespace HnrAzevedo\Validator;

Class User{

    public function __construct()
    {

        Validator::add($this, function(Rules $rules){
            $rules->setAction('login')
                  ->addField('email',['minlength'=>1,'regex'=>'/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/','required'=>true])
                  ->addField('password',['index'=>2,'minlength'=>6,'maxlength'=>20,'required'=>true])
				  ->addField('remember',['index'=>3,'minlength'=>2,'maxlength'=>2,'required'=>false]);

			return $rules;
        });

        return $this;
    }

}

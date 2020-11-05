<?php

namespace HnrAzevedo\Validator\Example\Rules;

use HnrAzevedo\Validator\Validator;
use HnrAzevedo\Validator\Rules;

Class User
{
    public function __construct()
    {
        Validator::add($this, function(Rules $rules){
            $rules->action('login')
                  ->field(
                      'field_email',
                      ['minlength' => 1, 'filter' => FILTER_VALIDATE_EMAIL, 'regex' => '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', 'required' => true],
                      'Email address'
                    )
                  ->field(
                      'field_password',
                      ['minlength' => 6, 'maxlength' => 20, 'required' => true],
                      'Password'
                    )
                  ->field(
                      'field_password2',
                      ['minlength' => 6, 'maxlength' => 20, 'equals' => 'field_password', 'required' => true],
                      'Confirm password'
                    )
                  ->field(
                      'field_remember',
                      ['minlength' => 2, 'maxlength' => 2, 'required' => false],
                      'Keep connected'
                    )
                  ->field(
                      'field_birth',
                      ['type' => 'date', 'required' => true],
                      'Date of birth'
                    )
                  ->field(
                      'field_phones',
                      ['mincount' => 2, 'maxcount' => 3, 'required' => true, 'minlength' => 8, 'maxlength' => 9],
                      'Phones'
                    );

			return $rules;
        });
    }

}

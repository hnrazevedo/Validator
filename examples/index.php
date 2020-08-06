<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/config.php';

use HnrAzevedo\Validator\Validator;

/* NOTE: in case of error an exception is thrown */

try{
    /* Required format for validation */
    $data = [
        'data' => json_encode([
            'email'=> 'hnr.azevedo@gmail.com',
            'password' => 123456,
            'password2' => 123456
        ]),
        'provider' => 'user',
        'role' => 'login'
    ];

    /* Checks whether the passed data is valid for the selected function */
    $valid = Validator::execute($data);

    /* Transforms validation to Json format to be validated on the client if desired */
    $json = Validator::toJson($data);


}catch(Exception $er){

    die($er->getCode().'  -  '.$er->getMessage());

}
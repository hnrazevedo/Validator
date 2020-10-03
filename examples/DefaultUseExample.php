<?php

require __DIR__.'/../vendor/autoload.php';

use HnrAzevedo\Validator\Validator;

/* Required format for validation */
$data = [
    'email'=> 'hnr.azevedo@gmail.com',
    'password' => 123456,
    'password2' => 123456,
    'phones' => [
        '949164770','949164771','949164772'
    ],
    'birth' => '28/09/1996',
    'PROVIDER' => 'user',
    'ROLE' => 'login'
];

try{

    /* Checks whether the passed data is valid for the selected function */
    $valid = Validator::namespace('Example\\Rules')->execute($data);
    
    if(!$valid){
        $errors = [];
        foreach(Validator::getErrors() as $err => $message){
            $errors[] = [
                'input' => array_keys($message)[0],                 // Return name input error
                'message' => $message[array_keys($message)[0]]      // Return message error
            ];
        }
    }

    /* Transforms validation to Json format to be validated on the client if desired */
    $json = Validator::namespace('Example\\Rules')->toJson($data);

    var_dump($json);

}catch(Exception $er){

    die("Code Error: {$er->getCode()}<br> Line: {$er->getLine()}<br> File: {$er->getFile()}<br> Message: {$er->getMessage()}");

}

<?php

require __DIR__.'/../vendor/autoload.php';

use HnrAzevedo\Validator\Validator;

try{

    /* Checks whether the passed data is valid for the selected function */
    $valid = Validator::namespace('HnrAzevedo\\Validator\\Example\\Rules')->lang('pt_br')->execute($data);
    
    $errors = [];

    if(!$valid){
        foreach(Validator::getErrors() as $err => $message){
            
            $errors[] = [
                'input' => $err,                 // Return name input error
                'message' => $message            // Return message error
            ];
        }

        var_dump($errors);
    }

    /* Transforms validation to Json format to be validated on the client if desired */
    $json = Validator::namespace('HnrAzevedo\\Validator\\Example\\Rules')->toJson($data);

}catch(Exception $er){

    die("Code Error: {$er->getCode()}<br> Line: {$er->getLine()}<br> File: {$er->getFile()}<br> Message: {$er->getMessage()}");

}

<?php

//echo '<pre>';

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

//require "DefaultUseExample.php";
require "MiddlewareUseExample.php";
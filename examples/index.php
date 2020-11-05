<?php

/* Required format for validation */
$data = [
    'field_email'=> 'hnr.azevedo@gmail.com',
    'field_password' => '123456',
    'field_password2' => '1234567',
    'field_phones' => [
        '949164770','949164771','949164772'
    ],
    'field_birth' => '28/09/1996',
    '_PROVIDER' => 'user',
    '_ROLE' => 'login'
];

require "DefaultUseExample.php";
require "MiddlewareUseExample.php";
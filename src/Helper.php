<?php

namespace HnrAzevedo\Validator;

trait Helper
{
    protected static Validator $instance;
    
    private static function getInstance(): Validator
    {
        self::$instance = (isset(self::$instance)) ? self::$instance : new Validator();
        return self::$instance;
    }

}

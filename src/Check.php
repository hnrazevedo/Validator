<?php

namespace HnrAzevedo\Validator;

use Exception;

Trait Check{
    protected static array $data = [];
    protected static array $validators = [];
    protected static string $model = '';

    protected static function check_minlength(string $param, $value)
    {
        if(self::toNext($param,$value)){
            if(strlen($value)===0){
                throw new Exception("O campo '{$param}' é obrigatório.",1);
            }
             
            if($value < intval(self::$validators[self::$model]->getRules(self::$data['role'])[$param]['minlength'])) {
                throw new Exception("{$param} não atingiu o mínimo de caracteres esperado.",1);
            }
        }       
    }

    protected static function check_regex(string $param, $value)
    {
        if(self::toNext($param,$value)){
            if(!@preg_match(self::$validators[self::$model]->getRules(self::$data['role'])[$param]['regex'], json_decode(self::$data['data'])->$param)){
                throw new Exception("{$param} inválido(a).",1);
            }  
        }       
    }

    protected static function check_index(string $param, $value)
    {
        if(self::toNext($param,$value)){
            
        }
    }

    protected static function check_equals(string $param, $value)
    {
        if(self::toNext($param,$value)){

            if(!array_key_exists($param,json_decode(self::$data['data'],true))){
                throw new Exception("O servidor não encontrou a informação '{$value}' para ser comparada a '{$param}'.",1);
            }
            
            if(json_decode(self::$data['data'])->$param != json_decode(self::$data['data'],true)[$value]){
                throw new Exception(ucfirst($param).' está diferente de '.ucfirst($value),1);
            }

        }       
    }

    protected static function check_maxlength(string $param, $value)
    {
        if(self::toNext($param,$value)){
            if($value > intval(self::$validators[self::$model]->getRules(self::$data['role'])[$param]['maxlength'])) {
                throw new Exception("{$param} ultrapassou o limite de caracteres permitidos.",1);
            }
        }       
    }

    protected static function check_type(string $param, $value)
    {
        if(self::toNext($param,$value)){
            /*
            var_dump($value);
                    switch ($value) {
                        case 'date':
                            $date = explode('/', $valuee);
                            if(count($date) != 3){
                                throw new Exception('Data inválida.',1);
                            }
                            if(! checkdate( intval($date[1]), intval($date[0]), intval($date[2]) )){
                                throw new Exception('Data inválida.',1);
                            }
                            break;
                    }*/
        }       
    }

    protected static function check_required(string $param): bool
    {
        return (array_key_exists('required',self::$validators[self::$model]->getRules(self::$data['role'])[$param]) && self::$validators[self::$model]->getRules(self::$data['role'])[$param]['required']);
    }

    protected static function toNext(string $param, $value)
    {
        return (self::check_required($param) || strlen($value > 0));
    }

}
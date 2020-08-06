<?php

namespace HnrAzevedo\Validator;

use Exception;

Trait Check{
    protected static array $data = [];
    protected static array $validators = [];
    protected static string $model = '';
    protected static array $required = [];

    protected static function check_minlength(string $param, $value)
    {
        if(self::toNext($param,$value)){    
            
            $realval = (is_array(json_decode(self::$data['data'])->$param)) ? json_decode(self::$data['data'])->$param : [json_decode(self::$data['data'])->$param];

            foreach($realval as $val){
                if($value > strlen($val)) {
                    throw new Exception("{$param} não atingiu o mínimo de caracteres esperado.",1);
                }
            }
        }       
    }

    protected static function check_requireds()
    {
        if(count(self::$required) > 0){
            throw new Exception('As seguintes informações não poderam ser validadas: '.implode(', ',array_keys(self::$required)).'.');
        }
    }

    protected static function check_regex(string $param, $value)
    {
        if(self::toNext($param,$value)){

            $realval = (is_array(json_decode(self::$data['data'])->$param)) ? json_decode(self::$data['data'])->$param : [json_decode(self::$data['data'])->$param];

            foreach($realval as $val){

                if(!@preg_match(self::$validators[self::$model]->getRules(self::$data['role'])[$param]['regex'], $val)){
                    throw new Exception("{$param} inválido(a).",1);
                }  

            }
        }       
    }

    protected static function check_mincount(string $param, $value)
    {
        if(self::toNext($param,$value)){
            $array = self::testArray($param, json_decode(self::$data['data'])->$param);
            if(count($array) < $value){
                throw new Exception("{$param} não atingiu o mínimo esperado.",1);
            }
        }
    }

    protected static function check_maxcount(string $param, $value)
    {
        if(self::toNext($param,$value)){
            $array = self::testArray($param, json_decode(self::$data['data'])->$param);
            if(count($array) > $value){
                throw new Exception("{$param} ultrapassou o esperado.",1);
            }
        }
    }

    protected static function testArray(string $param, $value): ?array
    {
        if(!is_array($value)){
            throw new Exception("Era esperado um informação em array para {$param}.");
        }
        return $value;
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

            $realval = (is_array(json_decode(self::$data['data'])->$param)) ? json_decode(self::$data['data'])->$param : [json_decode(self::$data['data'])->$param];

            foreach($realval as $val){

                if($value < strlen($val)) {
                    throw new Exception("{$param} ultrapassou o máximo de caracteres esperado.",1);
                }
            
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
<?php

namespace HnrAzevedo\Validator;

Trait Check{
    protected static array $data = [];
    protected static array $validators = [];
    protected static string $model = '';
    protected static array $required = [];
    protected static array $errors = [];

    protected static function check_minlength(string $param, $value)
    {
        if(self::toNext($param,$value)){    
            
            $realval = (is_array(json_decode(self::$data['data'])->$param)) ? json_decode(self::$data['data'])->$param : [json_decode(self::$data['data'])->$param];

            foreach($realval as $val){
                if($value > strlen($val)) {
                    self::$errors[] = "{$param} não atingiu o mínimo de caracteres esperado.";
                }
            }
        }       
    }

    protected static function check_errors(): bool
    {
        return (count(self::$errors) === 0);
    }

    protected static function check_requireds()
    {
        if(count(self::$required) > 0){
            self::$errors[] = 'As seguintes informações não poderam ser validadas: '.implode(', ',array_keys(self::$required)).'.';
        }
    }

    protected static function check_regex(string $param, $value)
    {
        if(self::toNext($param,$value)){

            $realval = (is_array(json_decode(self::$data['data'])->$param)) ? json_decode(self::$data['data'])->$param : [json_decode(self::$data['data'])->$param];

            foreach($realval as $val){

                if(!preg_match(self::$validators[self::$model]->getRules(self::$data['role'])[$param]['regex'], $val)){
                    self::$errors[] = "{$param} inválido(a).";
                }  

            }
        }       
    }

    protected static function check_mincount(string $param, $value)
    {
        if(self::toNext($param,$value)){
            $array = self::testArray($param, json_decode(self::$data['data'])->$param);
            if(count($array) < $value){
                self::$errors[] = "{$param} não atingiu o mínimo esperado.";
            }
        }
    }

    protected static function check_maxcount(string $param, $value)
    {
        if(self::toNext($param,$value)){
            $array = self::testArray($param, json_decode(self::$data['data'])->$param);
            if(count($array) > $value){
                self::$errors[] = "{$param} ultrapassou o esperado.";
            }
        }
    }

    protected static function testArray(string $param, $value): ?array
    {
        if(!is_array($value)){
            self::$errors[] = "Era esperado um informação em array para {$param}.";
        }
        return $value;
    }

    protected static function check_equals(string $param, $value)
    {
        if(self::toNext($param,$value)){

            if(!array_key_exists($param,json_decode(self::$data['data'],true))){
                self::$errors[] = "O servidor não encontrou a informação '{$value}' para ser comparada a '{$param}'.";
            }
            
            if(json_decode(self::$data['data'])->$param != json_decode(self::$data['data'],true)[$value]){
                self::$errors[] = ucfirst($param).' está diferente de '.ucfirst($value);
            }

        }       
    }

    protected static function check_maxlength(string $param, $value)
    {
        if(self::toNext($param,$value)){

            $realval = (is_array(json_decode(self::$data['data'])->$param)) ? json_decode(self::$data['data'])->$param : [json_decode(self::$data['data'])->$param];

            foreach($realval as $val){

                if($value < strlen($val)) {
                    self::$errors[] = "{$param} ultrapassou o máximo de caracteres esperado.";
                }
        
            }
        }       
    }

    protected static function check_type(string $param, $value)
    {
        if(self::toNext($param,$value)){

            switch ($value) {
                case 'date':
                    if(!self::validateDate(json_decode(self::$data['data'])->$param , 'd/m/Y')){
                        self::$errors[] = "{$param} não é uma data válida.";
                    }
                    break;
            }
        }       
    }

    protected static function check_filter(string $param, $value)
    {
        if(self::toNext($param,$value)){

            if(!filter_var(json_decode(self::$data['data'])->$param, $value)){
                self::$errors[] = "{$param} não passou pela filtragem de dados.";
            }

        }
    }

    public static function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
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
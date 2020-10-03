<?php

namespace HnrAzevedo\Validator;

Trait Check
{
    use ExtraCheck;

    protected static function checkMinlength(string $param, $value): void
    {
        if(self::toNext($param,$value)){    
            
            $realval = (is_array(self::$data[$param])) ? self::$data[$param] : [self::$data[$param]];

            foreach($realval as $val){
                if(strlen($val) === 0) {
                    self::$errors[] = [
                        $param => 'é obrigatório.'
                    ];
                    continue;
                }
                if($value > strlen($val)) {
                    self::$errors[] = [
                        $param => 'não atingiu o mínimo de caracteres esperado.'
                    ];
                }
            }
        }       
    }

    protected static function checkRegex(string $param, $value): void
    {
        if(self::toNext($param,$value)){

            $realval = (is_array(self::$data[$param])) ? self::$data[$param] : [self::$data[$param]];

            foreach($realval as $val){

                if(!preg_match(self::$validators[self::$model]->getRules(self::$data['ROLE'])[$param]['regex'], $val)){
                    self::$errors[] = [
                        $param => 'inválido(a).'
                    ];
                }  

            }
        }       
    }

    protected static function checkMincount(string $param, $value): void
    {
        if(self::toNext($param,$value)){
            $array = self::testArray($param, self::$data[$param]);
            if(count($array) < $value){
                self::$errors[] = [
                    $param => 'não atingiu o mínimo esperado.'
                ];
            }
        }
    }

    protected static function checkMaxcount(string $param, $value): void
    {
        if(self::toNext($param,$value)){
            $array = self::testArray($param, self::$data[$param]);
            if(count($array) > $value){
                self::$errors[] = [
                    $param => 'ultrapassou o esperado.'
                ];
            }
        }
    }

    protected static function checkEquals(string $param, $value): void
    {
        if(self::toNext($param,$value)){

            if(!array_key_exists($param,json_decode(self::$data['data'],true))){
                self::$errors[] = [
                    $param => "O servidor não encontrou a informação '{$value}' para ser comparada."
                ];
            }
            
            if(self::$data[$param] != json_decode(self::$data['data'],true)[$value]){
                self::$errors[] = [
                    $param => 'está diferente de '.ucfirst($value)
                ];
            }

        }       
    }

    protected static function checkMaxlength(string $param, $value)
    {
        if(self::toNext($param,$value)){

            $realval = (is_array(self::$data[$param])) ? self::$data[$param] : [self::$data[$param]];

            foreach($realval as $val){

                if($value < strlen($val)) {
                    self::$errors[] = [
                        $param => 'ultrapassou o máximo de caracteres esperado.'
                    ];
                }
        
            }
        }       
    }

    protected static function checkType(string $param, $value)
    {
        if(self::toNext($param,$value)){

            switch ($value) {
                case 'date':
                    if(!self::validateDate(self::$data[$param] , 'd/m/Y')){
                        self::$errors[] = [
                            $param => 'não é uma data válida.'
                        ];
                    }
                    break;
            }
        }       
    }

    protected static function checkFilter(string $param, $value)
    {
        if(self::toNext($param,$value)){

            if(!filter_var(self::$data[$param], $value)){
                self::$errors[] = [
                    $param => 'não passou pela filtragem de dados.'
                ];
            }

        }
    }

}

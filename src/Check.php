<?php

namespace HnrAzevedo\Validator;

Trait Check
{
    use ExtraCheck,
        Helper;

    protected function checkMinlength(string $param, $value): void
    {
        if(self::getInstance()->toNext($param, $value)){    
            
            $realval = (is_array(self::getInstance()->data($param))) ? self::getInstance()->data($param) : [self::getInstance()->data($param)];

            foreach($realval as $val){
                if(strlen($val) === 0) {
                    self::getInstance()->error([
                        $param => ' é obrigatório'
                    ]);
                    continue;
                }
                if($value > strlen($val)) {
                    self::getInstance()->error([
                        $param => 'não atingiu o mínimo de caracteres esperado'
                    ]);
                }
            }
        }       
    }

    protected function checkRegex(string $param, $value): void
    {
        if(self::getInstance()->toNext($param, $value)){

            $realval = (is_array(self::getInstance()->data($param))) ? self::getInstance()->data($param) : [self::getInstance()->data($param)];

            foreach($realval as $val){
                if(!preg_match(self::getInstance()->validator(self::getInstance()->model())->getRules(self::getInstance()->data('ROLE'))[$param]['regex'], $val)){
                    self::getInstance()->error([
                        $param => 'inválido(a)'
                    ]);
                }  
            }
        }       
    }

    protected function checkMincount(string $param, $value): void
    {
        if(self::getInstance()->toNext($param, $value)){
            $array = self::getInstance()->testArray($param, self::getInstance()->data($param));
            if(count($array) < $value){
                self::getInstance()->error([
                    $param => 'não atingiu o mínimo esperado'
                ]);
            }
        }
    }

    protected function checkMaxcount(string $param, $value): void
    {
        if(self::getInstance()->toNext($param, $value)){
            $array = self::getInstance()->testArray($param, self::getInstance()->data($param));
            if(count($array) > $value){
                self::getInstance()->error([
                    $param => 'ultrapassou o esperado'
                ]);
            }
        }
    }

    protected function checkEquals(string $param, $value): void
    {
        if(self::getInstance()->toNext($param, $value)){

            if(!array_key_exists($param, self::getInstance()->data())){
                self::getInstance()->error([
                    $param => "O servidor não encontrou a informação '{$value}' para ser comparada"
                ]);
            }
            
            if(self::getInstance()->data($param) != self::getInstance()->data($value)){
                self::getInstance()->error([
                    $param => 'está diferente de '.ucfirst($value)
                ]);
            }

        }       
    }

    protected function checkMaxlength(string $param, $value)
    {
        if(self::getInstance()->toNext($param, $value)){

            $realval = (is_array(self::getInstance()->data($param))) ? self::getInstance()->data($param) : [self::getInstance()->data($param)];

            foreach($realval as $val){

                if($value < strlen($val)) {
                    self::getInstance()->error([
                        $param => 'ultrapassou o máximo de caracteres esperado'
                    ]);
                }
        
            }
        }       
    }

    protected function checkType(string $param, $value)
    {
        if(self::getInstance()->toNext($param, $value)){

            switch ($value) {
                case 'date':
                    if(!self::getInstance()->validateDate(self::getInstance()->data($param) , 'd/m/Y')){
                        self::getInstance()->error([
                            $param => 'não é uma data válida'
                        ]);
                    }
                    break;
            }
        }       
    }

    protected function checkFilter(string $param, $value)
    {
        if(self::getInstance()->toNext($param, $value)){

            if(!filter_var(self::getInstance()->data($param), $value)){
                self::getInstance()->error([
                    $param => 'não passou pela filtragem de dados'
                ]);
            }

        }
    }

}

<?php

namespace HnrAzevedo\Validator;

Trait Check
{
    use ExtraCheck,
        Helper;

    protected array $data = [];
    protected array $validators = [];
    protected string $model = '';
    protected array $required = [];
    protected array $errors = [];

    protected function checkMinlength(string $param, $value): void
    {
        if(self::getInstance()->toNext($param, $value)){    
            
            $realval = (is_array(self::getInstance()->data[$param])) ? self::getInstance()->data[$param] : [self::getInstance()->data[$param]];

            foreach($realval as $val){
                if(strlen($val) === 0) {
                    self::getInstance()->errors[] = [
                        $param => 'é obrigatório'
                    ];
                    continue;
                }
                if($value > strlen($val)) {
                    self::getInstance()->errors[] = [
                        $param => 'não atingiu o mínimo de caracteres esperado'
                    ];
                }
            }
        }       
    }

    protected function checkRegex(string $param, $value): void
    {
        if(self::getInstance()->toNext($param, $value)){

            $realval = (is_array(self::getInstance()->data[$param])) ? self::getInstance()->data[$param] : [self::getInstance()->data[$param]];

            foreach($realval as $val){

                if(!preg_match(self::getInstance()->validators[self::getInstance()->model]->getRules(self::getInstance()->data['ROLE'])[$param]['regex'], $val)){
                    self::getInstance()->errors[] = [
                        $param => 'inválido(a)'
                    ];
                }  

            }
        }       
    }

    protected function checkMincount(string $param, $value): void
    {
        if(self::getInstance()->toNext($param, $value)){
            $array = self::getInstance()->testArray($param, self::getInstance()->data[$param]);
            if(count($array) < $value){
                self::getInstance()->errors[] = [
                    $param => 'não atingiu o mínimo esperado'
                ];
            }
        }
    }

    protected function checkMaxcount(string $param, $value): void
    {
        if(self::getInstance()->toNext($param, $value)){
            $array = self::getInstance()->testArray($param, self::getInstance()->data[$param]);
            if(count($array) > $value){
                self::getInstance()->errors[] = [
                    $param => 'ultrapassou o esperado'
                ];
            }
        }
    }

    protected function checkEquals(string $param, $value): void
    {
        if(self::getInstance()->toNext($param, $value)){

            if(!array_key_exists($param, self::getInstance()->data)){
                self::getInstance()->errors[] = [
                    $param => "O servidor não encontrou a informação '{$value}' para ser comparada"
                ];
            }
            
            if(self::getInstance()->data[$param] != self::getInstance()->data[$value]){
                self::getInstance()->errors[] = [
                    $param => 'está diferente de '.ucfirst($value)
                ];
            }

        }       
    }

    protected function checkMaxlength(string $param, $value)
    {
        if(self::getInstance()->toNext($param, $value)){

            $realval = (is_array(self::getInstance()->data[$param])) ? self::getInstance()->data[$param] : [self::getInstance()->data[$param]];

            foreach($realval as $val){

                if($value < strlen($val)) {
                    self::getInstance()->errors[] = [
                        $param => 'ultrapassou o máximo de caracteres esperado'
                    ];
                }
        
            }
        }       
    }

    protected function checkType(string $param, $value)
    {
        if(self::getInstance()->toNext($param, $value)){

            switch ($value) {
                case 'date':
                    if(!self::getInstance()->validateDate(self::getInstance()->data[$param] , 'd/m/Y')){
                        self::getInstance()->errors[] = [
                            $param => 'não é uma data válida'
                        ];
                    }
                    break;
            }
        }       
    }

    protected function checkFilter(string $param, $value)
    {
        if(self::getInstance()->toNext($param, $value)){

            if(!filter_var(self::getInstance()->data[$param], $value)){
                self::getInstance()->errors[] = [
                    $param => 'não passou pela filtragem de dados'
                ];
            }

        }
    }

}

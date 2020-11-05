<?php

namespace HnrAzevedo\Validator;

Trait Check
{
    use ExtraCheck,
        Helper;

    protected function minlength(string $param, int $value, array $rules): void
    {
        if(!self::getInstance()->valid($param, self::getInstance()->data($param))){ 
            return;
        }

        foreach( self::array(self::getInstance()->data($param)) as $val ){

            if(strlen($val) === 0) {
                self::getInstance()->error([ $param => $rules[$param]['placeholder'] . self::$err['required'] ]);
                continue;
            }

            if($value > strlen($val)){
                self::getInstance()->error([ $param => $rules[$param]['placeholder'] . self::$err['minlength'] ]);
            }

        }

    }

    protected function regex(string $param, string $value, array $rules): void
    {
        if(!self::getInstance()->valid($param, self::getInstance()->data($param))){
            return;
        }

        foreach(self::array(self::getInstance()->data($param)) as $val){
            if(!preg_match($value, $val)){
                self::getInstance()->error([ $param => $rules[$param]['placeholder'] . self::$err['regex'] ]);
            }  
        }  
    }

    protected function mincount(string $param, string $value, array $rules): void
    {
        if(!self::getInstance()->valid($param, self::getInstance()->data($param))){
            return;
        }

        if(count(self::array(self::getInstance()->data($param))) < intval($value)){
            self::getInstance()->error([ $param => $rules[$param]['placeholder'] . self::$err['mincount'] ]);
        }
        
    }

    protected function maxcount(string $param, string $value, array $rules): void
    {
        if(!self::getInstance()->valid($param, self::getInstance()->data($param))){
            return;
        }
        
        if(count(self::array(self::getInstance()->data($param))) > intval($value)){
            self::getInstance()->error([ $param => $rules[$param]['placeholder'] . self::$err['maxcount'] ]);
        }
        
    }

    protected function equals(string $param, string $value, array $rules): void
    {
        if(!array_key_exists($param, self::getInstance()->data())){
            self::getInstance()->error([ $param => $rules[$value]['placeholder'] . self::$err['nFoundEquals'] ]);
            return;
        }
            
        if(self::getInstance()->data($param) != self::getInstance()->data($value)){
            self::getInstance()->error([ $param => $rules[$param]['placeholder'] . self::$err['equals'] . $rules[$value]['placeholder'] ]);
        } 
    }

    protected function maxlength(string $param, string $value, array $rules): void
    {
        if(!self::getInstance()->valid($param, self::getInstance()->data($param))){
            return;
        }

        foreach( self::array(self::getInstance()->data($param)) as $val ){
            if(intval($value) < strlen($val)) {
                self::getInstance()->error([ $param => $rules[$param]['placeholder'] . self::$err['maxlength'] ]);
            }
        }
    }

    protected function type(string $param, string $value, array $rules): void
    {
        if(!self::getInstance()->valid($param, self::getInstance()->data($param))){
            return;
        }

        switch ($value) {
            case 'date':
                if(!self::getInstance()->validateDate(self::getInstance()->data($param) , 'd/m/Y')){
                    self::getInstance()->error([ $param => $rules[$param]['placeholder'] . self::$err['type'] ]);
                }
            break;
        }
           
    }

    protected function filter(string $param, string $value, array $rules): void
    {
        if(!self::getInstance()->valid($param, self::getInstance()->data($param))){
            return;
        }
            
        if(!filter_var(self::getInstance()->data($param), $value)){
            self::getInstance()->error([ $param => $rules[$param]['placeholder'] . self::$err['filter'] ]);
        }
        
    }

    protected function placeholder(): void
    {}

}

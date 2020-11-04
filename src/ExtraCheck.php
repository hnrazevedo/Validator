<?php

namespace HnrAzevedo\Validator;

Trait ExtraCheck
{
    use Helper;
    
    protected function requireds(): void
    {
        if(count(self::getInstance()->required()) > 0){
            self::getInstance()->error([ self::$err['needed'].implode(', ', array_keys(self::getInstance()->required())) ]);
        }
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    protected function isRequired(string $param): bool
    {
        return (array_key_exists('required', self::getInstance()->validator(self::getInstance()->model())->getRules(self::getInstance()->data('_ROLE'))[$param]) 
        && self::getInstance()->validator(self::getInstance()->model())->getRules(self::getInstance()->data('_ROLE'))[$param]['required']);
    }

    protected function valid(string $param, $value): bool
    {
        return (self::getInstance()->isRequired($param) || strlen($value > 0));
    }

}

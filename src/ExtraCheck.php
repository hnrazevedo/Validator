<?php

namespace HnrAzevedo\Validator;

Trait ExtraCheck{
    protected static array $data = [];
    protected static array $validators = [];
    protected static string $model = '';
    protected static array $required = [];
    protected static array $errors = [];

    protected static function check_requireds()
    {
        if(count(self::$required) > 0){
            self::$errors[] = [
                'As seguintes informações não poderam ser validadas: '.implode(', ',array_keys(self::$required)).'.'
            ];
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

    protected static function testArray(string $param, $value): ?array
    {
        if(!is_array($value)){
            self::$errors[] = [
                $param => 'Era esperado um informação em array para está informação.'
            ];
            return [];
        }
        return $value;
    }
}

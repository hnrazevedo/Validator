<?php

namespace HnrAzevedo\Validator;

Trait ExtraCheck
{
    protected static array $data = [];
    protected static array $validators = [];
    protected static string $model = '';
    protected static array $required = [];
    protected static array $errors = [];

    protected static function checkRequireds(): void
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

    protected static function checkRequired(string $param): bool
    {
        return (array_key_exists('required',self::$validators[self::$model]->getRules(self::$data['ROLE'])[$param]) && self::$validators[self::$model]->getRules(self::$data['ROLE'])[$param]['required']);
    }

    protected static function toNext(string $param, $value): bool
    {
        return (self::checkRequired($param) || strlen($value > 0));
    }

    protected static function testArray(string $param, $value): ?array
    {
        if(!is_array(json_decode($value))){
            self::$errors[] = [
                $param => 'Era esperado uma informação em formato array para está informação.'
            ];
            return [];
        }
        return json_decode($value);
    }
}

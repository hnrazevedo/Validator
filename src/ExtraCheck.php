<?php

namespace HnrAzevedo\Validator;

Trait ExtraCheck
{
    use Helper;
    
    protected array $data = [];
    protected array $validators = [];
    protected string $model = '';
    protected array $required = [];
    protected array $errors = [];

    protected function checkRequireds(): void
    {
        if(count(self::getInstance()->required) > 0){
            self::getInstance()->errors[] = [
                'As seguintes informações não poderam ser validadas: '.implode(', ',array_keys(self::getInstance()->required))
            ];
        }
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    protected function checkRequired(string $param): bool
    {
        return (array_key_exists(
            'required',
            self::getInstance()->validators[self::getInstance()->model]->getRules(self::getInstance()->data['ROLE'])[$param]) 
        && self::getInstance()->validators[self::getInstance()->model]->getRules(self::getInstance()->data['ROLE'])[$param]['required']);
    }

    protected function toNext(string $param, $value): bool
    {
        return (self::getInstance()->checkRequired($param) || strlen($value > 0));
    }

    protected function testArray(string $param, $value): ?array
    {
        if(!is_array($value)){
            self::getInstance()->errors[] = [
                $param => 'Era esperado uma informação em formato array para está informação'
            ];
            return [];
        }
        return $value;
    }
}

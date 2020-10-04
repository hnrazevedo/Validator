<?php

namespace HnrAzevedo\Validator;

use HnrAzevedo\Validator\Rules;
use Psr\Http\Server\MiddlewareInterface;

Class Validator implements MiddlewareInterface
{
    use Check,
        ExtraCheck,
        MiddlewareTrait,
        Helper;

    private string $namespace = '';
    private array $defaultData = [
        'REQUEST_METHOD',
        'PROVIDER',
        'ROLE'
    ];

    public static function add(object $model, \Closure $return): void
    {
        self::getInstance()->model = get_class($model);
        self::getInstance()->validator(self::getInstance()->model, $return(new Rules($model)));
    }

    private static function getClass(string $class)
    {
        if(!class_exists($class)){
            throw new \RuntimeException("Form ID {$class} inválido");
        }

        $class = get_class(new $class());

        return $class;
    }

    private function existRole($rules)
    {
        if(empty(self::getInstance()->validator($rules)->getRules(self::getInstance()->data['ROLE']))){
            throw new \RuntimeException('Não existe regras para validar este formulário');
        }
    }

    public function checkDatas(array $data): void
    {
        if(!isset($data['PROVIDER']) || !isset($data['ROLE'])){
            throw new \RuntimeException('The server did not receive the information needed to retrieve the requested validator');
        }
    }

    public static function execute(array $data): bool
    {
        try{
            self::getInstance()->checkDatas($data);

            self::getInstance()->data = $data;

            $model = self::getInstance()->namespace.'\\'.ucfirst(self::getInstance()->data['PROVIDER']);
                
            self::getInstance()->model = self::getInstance()->getClass($model);

            self::getInstance()->existRole(self::getInstance()->model);
                
            foreach ( (self::getInstance()->validator(self::getInstance()->model)->getRules($data['ROLE'])) as $key => $value) {
                if(@$value['required'] === true){
                    self::getInstance()->required[$key] = $value;
                }
            }

            self::getInstance()->errors = [];
        
            self::getInstance()->validate();
            self::getInstance()->checkRequireds();
        }catch(\Exception $er){
            self::getInstance()->errors[] = $er->getMessage();
        }
        
		return self::checkErrors();
    }

    public static function checkErrors(): bool
    {
        return (count(self::getInstance()->errors) === 0);
    }
    
    public function validate(): void
    {
        foreach ( (self::getInstance()->validator(self::getInstance()->model)->getRules(self::getInstance()->data['ROLE'])) as $key => $value) {

			foreach (self::getInstance()->data as $keyy => $valuee) {

				self::getInstance()->checkExpected($keyy);

				if($keyy===$key){

                    unset(self::getInstance()->required[$key]);

					foreach ($value as $subkey => $subvalue) {
                        $function = "check".ucfirst($subkey);
                        self::getInstance()->testMethod($function);
                        self::getInstance()->$function($keyy, $subvalue);
					}
				}
			}
        }
    }

    private function checkExpected(string $keyy): void
    {
        if(!array_key_exists($keyy, (self::getInstance()->validator(self::getInstance()->model)->getRules(self::getInstance()->data['ROLE'])) ) && !in_array($keyy, self::getInstance()->defaultData)){
            throw new \RuntimeException("O campo '{$keyy}' não é esperado para está operação");
        }
    }

    public static function getErrors(): array
    {
        return self::getInstance()->errors;
    }

    public function testMethod($method): void
    {
        if(!method_exists(static::class, $method)){
            throw new \RuntimeException("{$method} não é uma validação válida");
        }
    }

    public static function toJson(array $request): string
    { 
        $response = null;

        self::getInstance()->checkDatas($request);

        self::getInstance()->data['PROVIDER'] = $request['PROVIDER'];
        self::getInstance()->data['ROLE'] = $request['ROLE'];

        $model = self::getInstance()->namespace.'\\'.ucfirst($request['PROVIDER']);

        self::getInstance()->model = self::getClass($model);

        self::getInstance()->existRole(self::getInstance()->model);

		foreach ( self::getInstance()->validator(self::getInstance()->model)->getRules($request['ROLE'])  as $field => $r) {
            $r = self::getInstance()->replaceRegex($r);
            $response .= ("{$field}:".json_encode(array_reverse($r))).',';
        }

        return '{'.substr($response,0,-1).'}';
    }
    
    private function replaceRegex(array $rules): array
    {
        if(array_key_exists('regex', $rules)){ 
            $rules['regex'] = substr($rules['regex'], 1, -2);
        }
        return $rules;
    }

    public static function namespace(string $namespace): Validator
    {
        self::getInstance()->namespace = $namespace;
        return self::getInstance();
    }
}

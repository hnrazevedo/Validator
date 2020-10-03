<?php

namespace HnrAzevedo\Validator;

use HnrAzevedo\Validator\Rules;
use Psr\Http\Server\MiddlewareInterface;

Class Validator implements MiddlewareInterface
{
    use Check, MiddlewareTrait;

    private static Validator $instance;
    private string $namespace = '';
    private array $defaultData = [
        'REQUEST_METHOD',
        'PROVIDER',
        'ROLE'
    ];

    private static function getInstance(): Validator
    {
        self::$instance = (isset(self::$instance)) ? self::$instance : new self();
        return self::$instance;
    }

    public static function add(object $model,callable $return): void
    {
        self::$model = get_class($model);
        self::$validators[self::$model] = $return(new Rules($model));
    }

    private static function getClass(string $class)
    {
        if(!class_exists($class)){
            throw new \RuntimeException("Form ID {$class} inválido");
        }

        $class = get_class(new $class());

        return $class;
    }

    private static function existRole($rules)
    {
        if(empty(self::$validators[$rules]->getRules(self::$data['ROLE']))){
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

            self::$data = $data;

            $model = self::getInstance()->namespace.'\\'.ucfirst(self::$data['PROVIDER']);
                
            self::$model = self::getClass($model);

            self::existRole(self::$model);
                
            foreach ( (self::$validators[self::$model]->getRules($data['ROLE'])) as $key => $value) {
                if(@$value['required'] === true){
                    self::$required[$key] = $value;
                }
            }

            self::$errors = [];
        
            self::validate();
            self::checkRequireds();
        }catch(\Exception $er){
            self::$errors[] = $er->getMessage();
        }
        
		return self::checkErrors();
    }

    public static function checkErrors(): bool
    {
        return (count(self::$errors) === 0);
    }
    
    public static function validate(): void
    {
        foreach ( (self::$validators[self::$model]->getRules(self::$data['ROLE'])) as $key => $value) {

			foreach (self::$data as $keyy => $valuee) {

				if(!array_key_exists($keyy, (self::$validators[self::$model]->getRules(self::$data['ROLE'])) ) && !in_array($keyy,self::getInstance()->defaultData)){
                    throw new \RuntimeException("O campo '{$keyy}' não é esperado para está operação");
                }

				if($keyy===$key){

                    unset(self::$required[$key]);

					foreach ($value as $subkey => $subvalue) {
                        $function = "check".ucfirst($subkey);
                        self::testMethod($function);
                        self::$function($keyy,$subvalue);
					}
				}
			}
        }
    }

    public static function getErrors(): array
    {
        return self::$errors;
    }

    public static function testMethod($method)
    {
        if(!method_exists(static::class, $method)){
            throw new \RuntimeException("{$method} não é uma validação válida");
        }
    }

    public static function toJson(array $request): string
    { 
        $response = null;

        self::getInstance()->checkDatas($request);

        self::$data['PROVIDER'] = $request['PROVIDER'];
        self::$data['ROLE'] = $request['ROLE'];

        $model = self::getInstance()->namespace.'\\'.ucfirst($request['PROVIDER']);

        self::$model = self::getClass($model);

        self::existRole(self::$model);

		foreach ( self::$validators[self::$model]->getRules($request['ROLE'])  as $field => $r) {
            $r = self::replaceRegex($r);
            $response .= ("{$field}:".json_encode(array_reverse($r))).',';
        }

        return '{'.substr($response,0,-1).'}';
    }
    
    private static function replaceRegex(array $rules): array
    {
        if(array_key_exists('regex',$rules)){ 
            $rules['regex'] = substr($rules['regex'],1,-2);
        }
        return $rules;
    }

    public static function namespace(string $namespace): Validator
    {
        self::getInstance()->namespace = $namespace;
        return self::getInstance();
    }
}

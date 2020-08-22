<?php

namespace HnrAzevedo\Validator;

use HnrAzevedo\Validator\Rules;
use Exception;

Class Validator{
    use Check;

    public static function add(object $model,callable $return): void
    {
        self::$model = get_class($model);
        self::$validators[self::$model] = $return($Rules = new Rules($model));
    }
    
    private static function existData()
    {
        if(!array_key_exists('data', self::$data)){
            throw new Exception('Informações cruciais não foram recebidas.');
        }
    }

    private static function jsonData()
    {
        if(json_decode(self::$data['data']) === null){
            throw new Exception('O servidor recebeu as informações no formato esperado.');
        }
    }

    private static function hasProvider()
    {
        if(!array_key_exists('provider',self::$data)){
            throw new Exception('O servidor não recebeu o ID do formulário.');
        }
    }

    private static function hasRole()
    {
        if(!array_key_exists('role',self::$data)){
            throw new Exception('O servidor não conseguiu identificar a finalidade deste formulário.');
        }
    }

    private static function getClass(string $class)
    {
        if(!class_exists($class)){
		$class = basename($class);
            throw new Exception("Form ID {$class} inválido.");
        }

        return new $class();
    }

    private static function existRole($rules)
    {
        if(empty(self::$validators[$rules]->getRules(self::$data['role']))){
            throw new Exception('Não existe regras para validar este formulário.');
        }
    }

    public static function checkDatas()
    {
		self::existData();
        self::jsonData();
        self::hasProvider();
        self::hasRole();
    }

    public static function execute(array $datas): bool
    {
        self::$data = $datas;

        self::checkDatas();

        $model = VALIDATOR_CONFIG['rules.namespace'].'\\'.ucfirst(self::$data['provider']);
        if(!class_exists($model)){
            throw new Exception("No rules {$model} found.");
        }
            
        self::$model = $model();

		self::existRole(self::$model);
            
		foreach ( (self::$validators[self::$model]->getRules($datas['role'])) as $key => $value) {
            if(@$value['required'] === true){
                self::$required[$key] = $value;
            }
        }

        self::$errors = [];

        self::validate();
        
        self::checkRequireds();
				
		return self::checkErrors();
    }

    public static function checkErrors(): bool
    {
        return (count(self::$errors) === 0);
    }
    
    public static function validate()
    {
        foreach ( (self::$validators[self::$model]->getRules(self::$data['role'])) as $key => $value) {

			foreach (json_decode(self::$data['data']) as $keyy => $valuee) {

				if(!array_key_exists($keyy, (self::$validators[self::$model]->getRules(self::$data['role'])) )){
                    throw new Exception("O campo '{$keyy}' não é esperado para está operação.");
                }

				if($keyy===$key){

                    unset(self::$required[$key]);

					foreach ($value as $subkey => $subvalue) {
                        $function = "check_{$subkey}";
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
            throw new Exception("{$method} não é uma validação válida.");
        }
    }

    public static function toJson(array $request): string
    { 
        $response = null;

        self::$data['provider'] = $request['provider'];
        self::$data['role'] = $request['role'];

        self::includeValidations();

        self::$model = get_class( self::getClass('HnrAzevedo\\Validator\\'.ucfirst($request['provider'])) );

        self::existRole(self::$model);

		foreach ( self::$validators[self::$model]->getRules($request['role'])  as $field => $r) {
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
}

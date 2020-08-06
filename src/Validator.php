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

    private static function includeValidations()
    {
        if( file_exists(VALIDATOR_CONFIG['path'] . ucfirst(self::$data['provider']) . '.php') ){
            require_once(VALIDATOR_CONFIG['path'] . ucfirst(self::$data['provider']) . '.php');
        }
    }

    private static function getClass(string $class)
    {
        if(!class_exists($class)){
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

    public static function execute(array $datas): bool
    {
        self::$data = $datas;

		self::existData();
        self::jsonData();
        self::hasProvider();
        self::hasRole();
            
        self::includeValidations();

        self::$model = get_class(self::getClass('HnrAzevedo\\Validator\\'.ucfirst(self::$data['provider'])));

		self::existRole(self::$model);

        $tests = 0;
            
		foreach ( (self::$validators[self::$model]->getRules($datas['role'])) as $key => $value) {
		    $tests = (@$value['required'] === true ) ? $tests+1 : $tests;
        }

		$testeds = self::validate();
            
		if($tests > $testeds){
            throw new Exception('Alguma informação necessária não pode ser validada.');
        }
				
		return true;
    }
    
    public static function validate(): int
    {
        $validate = 0;
        foreach ( (self::$validators[self::$model]->getRules(self::$data['role'])) as $key => $value) {

			foreach (json_decode(self::$data['data']) as $keyy => $valuee) {

                $v = $valuee;
                    
				if(is_array($valuee)){
					$v = null;
					foreach ($valuee as $vvv) {
						$v .= $vvv;
					}
                }
                    
				$valuee = $v;

				if(!array_key_exists($keyy, (self::$validators[self::$model]->getRules(self::$data['role'])) )){
                    throw new Exception("O campo '{$keyy}' não é esperado para está operação.");
                }

				if($keyy===$key){

                    $validate++;

					foreach ($value as $subkey => $subvalue) {
                        $function = "check_{$subkey}";
                        self::$function($keyy,$subvalue);
					}
				}
			}
        }
        return $validate;
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
            
            $response .= ("{$field}:".json_encode(array_reverse($r))).',';
            
        }

        $response = '{'.substr($response,0,-1).'}';
        $response = str_replace(',"',',',$response);
        $response = str_replace('{"','',$response);
        $response = str_replace('":',':',$response);

		return $response;
	}
}

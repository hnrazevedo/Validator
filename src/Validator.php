<?php

namespace HnrAzevedo\Validator;

use HnrAzevedo\Validator\Rules;
use Exception;

Class Validator{
    
    private static array $validators = array();
    private static array $data = [];

    public static function add(object $model,callable $return): void
    {
        self::$validators[get_class($model)] = $return($Rules = new Rules($model));
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
        if(empty(self::$validators[get_class($rules)]->getRules(self::$data['role']))){
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

        $rules = self::getClass('HnrAzevedo\\Validator\\'.ucfirst(self::$data['provider']));

		self::existRole($rules);

		$validators = self::$validators[get_class($rules)]->getRules($datas['role']);

        $tests = 0;
            
		foreach ($validators as $key => $value) {
		    $tests = (@$value['required'] === true ) ? $tests+1 : $tests;
        }

		$testeds = self::validate($validators);
            
		if($tests > $testeds){
            throw new Exception('Alguma informação necessária não pode ser validada.');
        }
				
		return true;
    }
    
    public static function validate(array $validators): int
    {
        $validate = 0;
        foreach ($validators as $key => $value) {

			foreach (json_decode(self::$data['data']) as $keyy => $valuee) {

                $v = $valuee;
                    
				if(is_array($valuee)){
					$v = null;
					foreach ($valuee as $vvv) {
						$v .= $vvv;
					}
                }
                    
				$valuee = $v;

				if(!array_key_exists($keyy, $validators)){
                    throw new Exception("O campo '{$keyy}' não é esperado para está operação.");
                }

				if($keyy===$key){

                    $validate++;
                        
					foreach ($value as $subkey => $subvalue) {

						switch ($subkey) {
							case 'minlength':
                                if(array_key_exists('required', $value)){
                                    if($value['required'] or strlen($valuee)!==0){
                                        if(strlen($valuee)===0){
                                            throw new Exception("O campo '{$key}' é obrigatório.",1);
                                        }
                                         
                                        if(strlen($valuee) < (int) $subvalue){
                                            throw new Exception("{$key} não atingiu o mínimo de caracteres esperado.",1);
                                        }
                                    }
                                }
                                break;

                            case 'type':
                                if(array_key_exists('required', $value)){
                                    if($value['required'] or strlen($valuee)!==0){
                                        switch ($subvalue) {
                                            case 'date':
                                                $date = explode('/', $valuee);
                                                if(count($date) != 3){
                                                    throw new Exception('Data inválida.',1);
                                                }
                                                if(! checkdate( intval($date[1]), intval($date[0]), intval($date[2]) )){
                                                    throw new Exception('Data inválida.',1);
                                                }
                                                break;
                                        }
                                    }
                                }
    						    break;

							case 'maxlength':
                                if(array_key_exists('required', $value)){
                                    if($value['required'] or strlen($valuee)!==0){
                                        if(strlen($valuee)>(int)$subvalue){
                                            throw new Exception("{$key} ultrapassou o limite de caracteres permitidos.",1);
                                        }
                                    }
                                }
                                break;

							case 'regex':
                                if(array_key_exists('required', $value)){
                                    if($value['required'] or strlen($valuee)!==0){
                                        if(!@preg_match($subvalue,$valuee)){
                                            throw new Exception("{$key} inválido(a).",1);
                                        }
                                    }
                                }
                                break;

							case 'equals':
                                $equals = false;
                                foreach (self::$data as $ke => $sub) {
                                    if($ke===$subvalue){
                                        $equals=true;
                                        if($valuee !== $sub){
                                            throw new Exception(ucfirst($key).' está diferente de '.ucfirst($ke),1);
                                        }
                                    }
                                }
                                if(!$equals){
                                    throw new Exception("O servidor não encontrou a informação '{$subvalue}' para ser comparada a '{$key}'.",1);
                                }
                                break;
	    				}
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

        $rules = self::getClass('HnrAzevedo\\Validator\\'.ucfirst($request['provider']));

        self::existRole($rules);

        $r = self::$validators[get_class($rules)]->getRules($request['role']);

		foreach ( self::$validators[get_class($rules)]->getRules($request['role'])  as $field => $r) {
            
            $response .= ("{$field}:".json_encode(array_reverse($r))).',';
            
        }

        $response = '{'.substr($response,0,-1).'}';
        $response = str_replace(',"',',',$response);
        $response = str_replace('{"','',$response);
        $response = str_replace('":',':',$response);

		return $response;
	}
}

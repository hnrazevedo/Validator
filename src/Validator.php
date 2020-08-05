<?php

namespace HnrAzevedo\Validator;

use HnrAzevedo\Validator\Rules;
use Exception;

Class Validator{
    
    private static array $validators = array();

    public static function add(object $model,object $return): void
    {
        self::$validators[get_class($model)] = $return($Rules = new Rules($model));
	}

    public static function execute(array $datas): bool
    {
		$field_error = null;

		if(!array_key_exists('data', $datas)){
            throw new Exception('Informações cruciais não foram recebidas.');
        }

		if(gettype($datas['data']) != 'string' || @json_decode($datas['data']) === null){
            throw new Exception('O servidor recebeu as informações no formato esperado.');
        }
			    
		if(!array_key_exists('provider',$datas) || is_null($datas['provider'])){
            throw new Exception('O servidor não recebeu o ID do formulário.');
        }
			    
		if(!array_key_exists('role',$datas) || is_null($datas['role'])){
            throw new Exception('O servidor não conseguiu identificar a finalidade deste formulário.');
        }

        $role = $datas['role'];
            
        $data = (array) json_decode($datas['data']);
            
        $file = (VALIDATOR_CONFIG['path'].ucfirst($datas['provider']).'.php');

        if(file_exists($file)){
            require_once($file);
        }

        $class = 'HnrAzevedo\\Validator\\'.ucfirst($datas['provider']);

		if(!class_exists($class)){
            throw new Exception("Form ID {$class} inválido.");
        }

        $rules = new $class();
            
		if(get_class(self::$validators[get_class($rules)]) !== 'HnrAzevedo\Validator\Rules'){
            throw new Exception('Ocorreu algum erro e o servidor não pode identificar o responsável por validar o formulário submetido.');
        }

		if(empty(self::$validators[get_class($rules)]->getRules($role))){
            throw new Exception('Não existe regras para validar este formulário.');
        }

		$validators = self::$validators[get_class($rules)]->getRules($role);

        $tests = 0;
            
		foreach ($validators as $key => $value) {
		    $tests = (array_key_exists('required',$value) and $value['required']===true) ? $tests+1 : $tests;
		}

		$testeds = 0;

		foreach ($validators as $key => $value) {

			foreach ($data as $keyy => $valuee) {

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

                    $testeds++;
                        
					foreach ($value as $subkey => $subvalue) {

						$field_error = $key;

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
                                                if(!@checkdate($date[1],$date[0],$date[2])){
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
                                foreach ($data as $ke => $sub) {
                                    if($ke===$subvalue){
                                        $equals=true;
                                        if($valuee!==$sub)
                                            throw new \Exception(ucfirst($key).' está diferente de '.ucfirst($ke),1);
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
            
		if($tests>$testeds){
            throw new Exception('Alguma informação necessária não pode ser validada.');
        }
				
		return true;
	}

    public static function toJson(array $request): string
    {
        $file = (VALIDATOR_CONFIG['path'].ucfirst($request['provider']).'.php');

        if(file_exists($file)){
            require_once($file);
        }

        $class = 'HnrAzevedo\\Validator\\'.ucfirst($request['provider']);

		if(!class_exists($class)){
            throw new Exception("Form ID {$class} inválido.");
        }

		$model = new $class();

		if(get_class(self::$validators[get_class($model)]) !== 'HnrAzevedo\Validator\Rules'){
            throw new Exception('Ocorreu algum erro e o servidor não pode identificar o responsável por validar o formulário submetido.');
        }
		
		if(empty(self::$validators[get_class($model)]->getRules($request['role']))){
            throw new Exception('Formulário desativado: Não existem regras para validar este formulário.');
        }
		
		$validators = self::$validators[get_class($model)]->getRules($request['role']);

        /* For function to validate information in javascript */
        //$response = 'validate(document.querySelector(\'form[provider="'.$request['provider'].'"][role="'.$request['role'].'"]\'),{';
        $response = '[\'form[provider="'.$request['provider'].'"][role="'.$request['role'].'"]\',{';
		foreach ($validators as $field => $rules) {
			$response .= $field.':{';
			foreach(array_reverse($rules) as $rule => $value){
				$value = (gettype($value)==='string') ? '\''.$value.'\'' : $value;
				if(gettype($value)==='boolean'){
                    $value = ($value) ? 'true' : 'false';
                }
			    $value = ($rule=='regex') ? str_replace('\\','\\\\','\''.substr($value,2,strlen($value)-4).'\'') : $value;
				$response .= $rule.':'.$value.',';
			}
			$response .='},';
        }
        //return str_replace(',}','}',$response.'});');
		return str_replace(',}','}',$response.'}];');
	}
}

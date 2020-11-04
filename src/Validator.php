<?php

namespace HnrAzevedo\Validator;

use HnrAzevedo\Validator\getRules;
use Psr\Http\Server\MiddlewareInterface;

Class Validator implements MiddlewareInterface
{
    use Check,
        ExtraCheck,
        MiddlewareTrait,
        Helper;

    public function __construct()
    {
        require __DIR__.DIRECTORY_SEPARATOR.'languages'. DIRECTORY_SEPARATOR . self::$lang .'.php';
        self::$err = $VALIDATOR_LANG;
    }

    public static function lang(string $lang): Validator
    {
        return self::getInstance($lang);
    }

    private string $namespace = '';
    private array $defaultData = [
        '_METHOD',
        '_PROVIDER',
        '_ROLE'
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

    private function existRole($getRules)
    {
        if(empty(self::getInstance()->validator($getRules)->getRules(self::getInstance()->data['_ROLE']))){
            throw new \RuntimeException('Não existe regras para validar este formulário');
        }
    }

    public function checkDatas(array $data): void
    {
        if(!isset($data['_PROVIDER']) || !isset($data['_ROLE'])){
            throw new \RuntimeException('The server did not receive the information needed to retrieve the requested validator');
        }
    }

    public static function execute(array $data): bool
    {
        try{
            self::getInstance()->checkDatas($data);

            self::getInstance()->data = $data;

            $model = self::getInstance()->namespace.'\\'.ucfirst(self::getInstance()->data['_PROVIDER']);
                
            self::getInstance()->model = self::getInstance()->getClass($model);

            self::getInstance()->existRole(self::getInstance()->model);
                
            foreach ( (self::getInstance()->validator(self::getInstance()->model)->getRules($data['_ROLE'])) as $key => $value) {
                if(@$value['required'] === true){
                    self::getInstance()->required[$key] = $value;
                }
            }

            self::getInstance()->errors = [];
        
            self::getInstance()->validate();
            self::getInstance()->requireds();
        }catch(\Exception $er){
            self::getInstance()->errors[] = $er->getMessage();
        }
        
		return self::errors();
    }

    public static function errors(): bool
    {
        return (count(self::getInstance()->errors) === 0);
    }
    
    public function validate(): void
    {
        foreach ( (self::getInstance()->validator(self::getInstance()->model)->getRules(self::getInstance()->data['_ROLE'])) as $key => $value) {

			foreach (self::getInstance()->data as $keyy => $valuee) {

				self::getInstance()->checkExpected($keyy);

				if($keyy===$key){

                    unset(self::getInstance()->required[$key]);

					foreach ($value as $subkey => $subvalue) {

                        $function = strtolower($subkey);

                        self::getInstance()->hasMethod($function)->$function($keyy, $subvalue);
					}
				}
			}
        }
    }

    private function checkExpected(string $keyy): void
    {
        if(!array_key_exists($keyy, (self::getInstance()->validator(self::getInstance()->model)->getRules(self::getInstance()->data['_ROLE'])) ) && !in_array($keyy, self::getInstance()->defaultData)){
            throw new \RuntimeException($keyy . self::$err['nExpected']);
        }
    }

    public static function getErrors(): array
    {
        return self::getInstance()->errors;
    }

    public function hasMethod($method): Validator
    {
        if(!method_exists(static::class, $method)){
            throw new \RuntimeException($method . self::$err['nMethod']);
        }
        
        return $this;
    }

    public static function toJson(array $request): string
    { 
        $response = null;

        self::getInstance()->checkDatas($request);

        self::getInstance()->data['_PROVIDER'] = $request['_PROVIDER'];
        self::getInstance()->data['_ROLE'] = $request['_ROLE'];

        $model = self::getInstance()->namespace.'\\'.ucfirst($request['_PROVIDER']);

        self::getInstance()->model(self::getClass($model));

        self::getInstance()->existRole(self::getInstance()->model());

		foreach ( self::getInstance()->validator(self::getInstance()->model())->getRules($request['_ROLE'])  as $field => $r) {
            $r = self::getInstance()->replaceRegex($r);
            $response .= ("'$field':".json_encode(array_reverse($r))).',';
        }

        return '{'.substr($response,0,-1).'}';
    }
    
    private function replaceRegex(array $getRules): array
    {
        if(array_key_exists('regex', $getRules)){ 
            $getRules['regex'] = substr($getRules['regex'], 1, -2);
        }

        return $getRules;
    }

    public static function namespace(string $namespace): Validator
    {
        self::getInstance()->namespace = $namespace;
        return self::getInstance();
    }
}

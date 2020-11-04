<?php

namespace HnrAzevedo\Validator;

Class Rules
{
    use Helper;

    private string $action;

    private array $form = array();

    public function __construct(object $model)
    {
	    $this->form['model'] = ucfirst(get_class($model));
	}

    public function action(string $action): Rules
    {
	    $this->action = $action;
	    return $this;
	}

    public function field(string $field, array $test, ?string $placeholder = null): Rules
    {
	    if(empty($this->action)){
            self::$errors[] = self::$err['nFoundForm'];
            return $this;
        }

        $this->form[$this->action][$field] = $test;
        $this->form[$this->action][$field]['placeholder'] = (null !== $placeholder) ? $placeholder : $test;
	    return $this;
  	}

    public function getRules(string $action): Array
    {
		return (array_key_exists($action, $this->form)) ? $this->form[$action] : [];
	}
}

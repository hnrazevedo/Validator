<?php

namespace HnrAzevedo\Validator;

use Exception;

Class Rules{
    private string $action;

    private array $form = array();

    public function __construct(object $model)
    {
	    $this->form['model'] = ucfirst(get_class($model));
	}

    public function setAction(string $action): Rules
    {
	    $this->action = $action;
	    return $this;
	}

    public function addField(string $field, array $test): Rules
    {
	    if(empty($this->action)){
            throw new Exception("Form action not registered.");
        }

	    if(empty($this->form[$this->action][$field])){
            $this->form[$this->action][$field] = $test;
        }

	    return $this;
  	}

    public function getRules(string $action): array
    {
		return (array_key_exists($action, $this->form)) ? $this->form[$action] : null;
	}
}

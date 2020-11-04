<?php

namespace HnrAzevedo\Validator;

trait Helper
{
    protected static Validator $instance;
    protected array $data = [];
    protected array $validators = [];
    protected string $model = '';
    protected array $required = [];
    protected array $errors = [];
    protected static string $lang = 'en';
    protected static array $err = [];


    protected static function array($value): Array
    {
        return (is_array($value)) ? $value : [ $value ];
    }

    protected static function getInstance(?string $lang = null): Validator
    {
        self::$lang = (null !== $lang) ? $lang : 'en';
        self::$instance = (isset(self::$instance)) ? self::$instance : new Validator();
        return self::$instance;
    }

    protected function error(array $error): void
    {
        $this->errors = array_merge($this->errors, $error);
    }

    protected function data($field = null, ?array $values = null)
    {
        if(null !== $values){
            $this->data[$field] = $values;
        }
        if(null !== $field){
            return $this->data[$field];
        }
        return $this->data;
    }

    protected function validator(string $model, ?object $callback = null)
    {
        if(null !== $callback){
            $this->validators[$model] = $callback;
        }
        return $this->validators[$model];
    }

    protected function model(?string $model = null): string
    {
        if(null !== $model){
            $this->model = $model;
        }
        return $this->model;
    }

    protected function required(): array
    {
        return $this->required;
    }

}

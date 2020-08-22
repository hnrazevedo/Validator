# Validator @HnrAzevedo

[![Maintainer](https://img.shields.io/badge/maintainer-@hnrazevedo-blue?style=flat-square)](https://github.com/hnrazevedo)
[![Latest Version](https://img.shields.io/github/v/tag/hnrazevedo/validator?label=version&style=flat-square)](Release)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/hnrazevedo/validator?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Validator/?branch=master)
[![Build Status](https://img.shields.io/scrutinizer/build/g/hnrazevedo/validator?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Validator/build-status/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/hnrazevedo/validator?style=flat-square)](https://packagist.org/packages/hnrazevedo/validator)
[![Total Downloads](https://img.shields.io/packagist/dt/hnrazevedo/validator?style=flat-square)](https://packagist.org/packages/hnrazevedo/validator)


###### Validator is a simple data validation component. Its author is not a professional in the development area, just someone in the Technology area who is improving his knowledge.

O Validator é um simples componente de validação de dados. Seu autor não é profissional da área de desenvolvimento, apenas alguem da área de Tecnologia que está aperfeiçoando seus conhecimentos.

### Highlights

- Easy to set up (Fácil de configurar)
- Current validation rules (Regras de validação atuais)
- Composer ready (Pronto para o composer)

## Installation

Validator is available via Composer:

```bash 
"hnrazevedo/validator": "^1.0"
```

or run

```bash
composer require hnrazevedo/validator
```

## Documentation

###### For details on how to use the Validator, see the sample folder with details in the component directory

Para mais detalhes sobre como usar o Validator, veja a pasta de exemplos com detalhes no diretório do componente

#### Configure

#### It is necessary to configure the namespace of the classes that set the validation rules
É necessário configurar o nomespace das classes que setaram as regras de validação

```php
define("VALIDATOR_CONFIG", [
    "rules.namespace" => "Rules"
]);
```

### Errors

#### In case of errors, Validator will throw a Exception.
Em casos de erros de configuração, o Validator disparara uma Exception.

### Validation rules

#### Available rules

- minlength: integer
- maxlength: integer
- required: boolean
- equals: string
- type: string
- mincount: integer - For arrays values
- maxcount: integer - For arrays values
- filter: integer - See https://www.php.net/manual/en/filter.filters.validate.php for more details of the available filters 
- regex: string

#### The validation rules must be removed when constructing the extended HnrAzevedor\Validator object
As regras de validação devem ser retiradas na construção do objeto extendido de HnrAzevedor\Validator

```php
namespace Rules;

use HnrAzevedo\Validator\Validator;
use HnrAzevedo\Validator\Rules;

Class User{

    public function __construct()
    {

        Validator::add($this, function(Rules $rules){
            $rules->setAction('login')
                  ->addField('email',['minlength'=>1,'filter'=>FILTER_VALIDATE_EMAIL,'required'=>true])
                  ->addField('password',['minlength'=>6,'maxlength'=>20,'required'=>true])
                  ->addField('password2',['equals'=>'password','required'=>true])
                  ->addField('remember',['minlength'=>2,'maxlength'=>2,'required'=>false])
                  ->addField('birth',['type'=>'date','required'=>true])
                  ->addField('phones',['mincount'=>2,'maxcount'=>3,'required'=>true,'minlength'=>8,'maxlength'=>9]);

			return $rules;
        });

        return $this;
    }

}
```

### Data format for validation

#### The data for validation must be passed to the component as follows
Os dados para validação devem ser passados ​​para o componente da seguinte forma

```php
$data = [
    'data' => json_encode([
        'email'=> 'hnr.azevedo@gmail.com',
        'password' => 123456,
        'password2' => 123456,
        'phones' => json_encode([
            '949164770','949164771','949164772'
        ]),
        'birth' => '28/09/1996' 
    ]),
    'provider' => 'user',   /* Class responsible for validations */
    'role' => 'login'       /* Form action */
];
```

### Check data

#### Validation errors are returned in an error array, in case there are more than one occurrence, they can be displayed at the same time
Os erros de validação são retornados em uma matriz de erro, caso haja mais de uma ocorrência, eles podem ser exibidos ao mesmo tempo

```php
$valid = Validator::execute($data);

if(!$valid){
    $errors = [];
    foreach(Validator::getErrors() as $err => $message){
        $errors[] = [
            'input' => array_keys($message)[0],                 // Return name input error
            'message' => $message[array_keys($message)[0]]      // Return message error
        ];
    }
}
```
### NOTE
#### In case of configuration error or improper receipt of data, Validator will throw an Exception.
Em caso de erro de configuração ou de recebimento indevido dos dados o Validator lançara uma Exception.

### toJson

#### Returns a readable Json for validation to be performed on the client side
Retorna um Json legível para validação a ser realizada no lado do cliente

```php
$json = Validator::toJson($data);

/**
 * Result:
 * {
 *     email:{required:true,filter:274,minlength:1},
 *     password:{required:true,maxlength:20,minlength:6},
 *     password2:{required:true,equals:"password"},
 *     remember:{required:false,maxlength:2,minlength:2},
 *     birth:{required:true,type:"date"},
 *     phones:{maxlength:9,minlength:8,required:true,maxcount:3,mincount:2}
 * }
 */
```

## Support

###### Security: If you discover any security related issues, please email hnrazevedo@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para hnrazevedo@gmail.com em vez de usar o rastreador de problemas.

## Credits

- [Henri Azevedo](https://github.com/hnrazevedo) (Developer)
- [Robson V. Leite](https://github.com/robsonvleite) (Readme based on your datalayer design)

## License

The MIT License (MIT). Please see [License File](https://github.com/hnrazevedo/Validator/blob/master/LICENSE.md) for more information.

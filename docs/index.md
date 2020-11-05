# Validator @HnrAzevedo

[![Maintainer](https://img.shields.io/badge/maintainer-@hnrazevedo-blue?style=flat-square)](https://github.com/hnrazevedo)
[![Latest Version](https://img.shields.io/github/v/tag/hnrazevedo/validator?label=version&style=flat-square)](https://github.com/hnrazevedo/Validator/releases)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/hnrazevedo/validator?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Validator/?branch=master)
[![Build Status](https://img.shields.io/scrutinizer/build/g/hnrazevedo/validator?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Validator/build-status/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/hnrazevedo/validator?style=flat-square)](https://packagist.org/packages/hnrazevedo/validator)
[![Total Downloads](https://img.shields.io/packagist/dt/hnrazevedo/validator?style=flat-square)](https://packagist.org/packages/hnrazevedo/validator)


##### The Validator is a simple data validation component. It can be used statically with a controller or together as middleware. Its author is not a professional in the development area, just someone in the Technology area who is improving his knowledge.

O Validator é um simples componente de validação de dados. Ele pode ser usado de forma estática com algum controlador ou em conjunto como middleware. Seu autor não é profissional da área de desenvolvimento, apenas alguem da área de Tecnologia que está aperfeiçoando seus conhecimentos.

### Highlights

- Easy to set up (Fácil de configurar)
- Current validation rules (Regras de validação atuais)
- Follows standard PSR-15 (Segue padrão o PSR-15)
- Composer ready (Pronto para o composer)

## Installation

Validator is available via Composer:

```bash 
"hnrazevedo/validator": "^2.1"
```

or in at terminal

```bash
composer require hnrazevedo/validator
```

## Documentation

##### For details on how to use the Validator, see the sample folder with details in the component directory
Para mais detalhes sobre como usar o Validator, veja a pasta de exemplos com detalhes no diretório do componente

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

### Languages

#### The system has been configured to support multiple languages, so make sure the desired language is in the languages folder and define it with the lang method before any procedure
O sistema foi configurado para suportar vários idiomas, então verifique se o idioma desejado se encontra na pasta languages e defina-o com o método lang antes de qualquer procedimento

```php
$valid = Validator::lang('pt_br')->namespace('App\\Rules')->execute($data);
```

## NOTE

#### In case the field is an array, the rules will be tested in all its elements.
Em caso do campo ser um array, as regras serão testadas em todos seus elementos.

#### The validation rules must be defined in the construction of the class object with the namespace denified in the Validator
As regras de validação devem ser definidas na construção do objeto da classe com o namespace denifida no Validator

```php
namespace App\Rules;

use HnrAzevedo\Validator\Validator;
use HnrAzevedo\Validator\Rules;

Class User{

    public function __construct()
    {
        Validator::add($this, function(Rules $rules){
            $rules->action('login')
                  /*
                   * @property string $inputName
                   * @property array $rules
                   * @property string $textPlaceholder
                  */
                  ->field('email',['minlength'=>1,'filter'=>FILTER_VALIDATE_EMAIL,'required'=>true],'Email address')
                  ->field('password',['minlength'=>6,'maxlength'=>20,'required'=>true],'Password')
                  ->field('password2',['equals'=>'password','required'=>true],'Confirm password')
                  ->field('remember',['minlength'=>2,'maxlength'=>2,'required'=>false])
                  ->field('birth',['type'=>'date','required'=>true],'Date of birth')
                  ->field('phones',['mincount'=>2,'maxcount'=>3,'required'=>true,'minlength'=>8,'maxlength'=>9]);
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
    'email'=> 'hnr.azevedo@gmail.com',
    'password' => 123456,
    'password2' => 123456,
    'phones' => [
        '949164770','949164771','949164772'
    ],
    'birth' => '28/09/1996' 
    'PROVIDER' => 'user',   /* Class responsible for validations */
    'ROLE' => 'login'       /* Form action */
];
```

### Defining namespace

#### To use dynamically to avoid the need to place the entire namespace of a class with rules, you must define the namespace before performing data validation
Para utilização de forma dinamica para evitar a necessidade de colocar o namespace inteiro de uma class com regras, deve-se definir o namespace antes de executar a validação dos dados

### Check data

#### Validation errors are returned in an error array, in case there are more than one occurrence, they can be displayed at the same time
Os erros de validação são retornados em uma matriz de erro, caso haja mais de uma ocorrência, eles podem ser exibidos ao mesmo tempo

```php
$valid = Validator::namespace('App\\Rules')->execute($data);

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

### toJson

#### Returns a readable Json for validation to be performed on the client side
Retorna um Json legível para validação a ser realizada no lado do cliente

```php
$json = Validator::namespace('App\\Rules')->toJson($data);

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

##### Security: If you discover any security related issues, please email hnr.azevedo@gmail.com instead of using the issue tracker.
Se você descobrir algum problema relacionado à segurança, envie um e-mail para hnr.azevedo@gmail.com em vez de usar o rastreador de problemas.

## Credits

- [Henri Azevedo](https://github.com/hnrazevedo) (Developer)

## License

The MIT License (MIT). Please see [License File](https://github.com/hnrazevedo/Validator/blob/master/LICENSE.md) for more information.
<?php

require __DIR__.'/../vendor/autoload.php';

use HnrAzevedo\Http\Factory;
use HnrAzevedo\Http\Uri;
use HnrAzevedo\Validator\Validator;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

try{
    $serverRequest = (new Factory())->createServerRequest('GET', new Uri('/'));
    $serverRequest = $serverRequest->withAttribute('validator',[
        'namespace' => 'HnrAzevedo\\Validator\\Example\\Rules',
        'data' => $data
    ]);

    class App implements MiddlewareInterface{
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            if(!$request->getAttribute('validator')['valid']){
                $err = '';
                foreach($request->getAttribute('validator')['errors'] as $er => $error){
                    $err .= (is_array($error)) ? implode('',array_keys($error)) . ' ' . implode('', array_values($error)) : $error;
                    $err .= ', ';
                }
                throw new \Exception(substr($err, 0, -2));
            }

            return $handler->handle($request);
        }
    }

    define('GLOBAL_MIDDLEWARES',[
        Validator::class,
        App::class
    ]);

    function nextExample(RequestHandlerInterface $defaultHandler): RequestHandlerInterface
    {
        return new class (GLOBAL_MIDDLEWARES, $defaultHandler) implements RequestHandlerInterface {
            private RequestHandlerInterface $handler;
            private array $pipeline;

            public function __construct(array $pipeline, RequestHandlerInterface $handler)
            {
                $this->handler = $handler;
                $this->pipeline = $pipeline;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                if (!$middleware = array_shift($this->pipeline)) {
                    return $this->handler->handle($request);
                }

                $next = clone $this;
                $this->pipeline = [];

                $response = (new $middleware())->process($request, $next);

                return $response;
            }
        };
    }


    function runMiddlewares($serverRequest)
    {
        nextExample(new class implements RequestHandlerInterface{
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new Factory())->createResponse(200);
            }
        })->handle($serverRequest);
    }

    runMiddlewares($serverRequest);

}catch(Exception $er){

    die("Code Error: {$er->getCode()}<br>Line: {$er->getLine()}<br>File: {$er->getFile()}<br>Message: {$er->getMessage()}.");

}

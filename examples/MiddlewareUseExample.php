<?php

session_start();

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/Routes/default.php';

use HnrAzevedo\Http\Factory;
use HnrAzevedo\Http\Uri;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use HnrAzevedo\Router\Router;
use Psr\Http\Server\MiddlewareInterface;

try{
    $serverRequest = (new Factory())->createServerRequest($_SERVER['REQUEST_METHOD'], new Uri($_SERVER['REQUEST_URI']));

    class App implements MiddlewareInterface{
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            if(empty($request->getAttribute('route')))
            {
                throw new Exception('Page not found', 404);
            }

            $request->getAttribute('route')['action']();

            return (new Factory())->createResponse(200);
        }
    }

    define('GLOBAL_MIDDLEWARES',[
        Router::class,
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

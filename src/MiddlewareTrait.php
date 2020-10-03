<?php

namespace HnrAzevedo\Validator;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

trait MiddlewareTrait{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(!self::execute($_REQUEST)){
            throw new \Exception(implode(', ',self::getErrors()));
        }

        return $handler->handle($request);
    }
    
}
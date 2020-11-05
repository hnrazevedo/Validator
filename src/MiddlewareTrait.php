<?php

namespace HnrAzevedo\Validator;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

trait MiddlewareTrait{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $data = ($request->getAttribute('validator') !== null && isset($request->getAttribute('validator')['data'])) ? $request->getAttribute('validator')['data'] : $_REQUEST;
        $namespace = ($request->getAttribute('validator') !== null && isset($request->getAttribute('validator')['namespace'])) ? $request->getAttribute('validator')['namespace'] : '';
        $lang = ($request->getAttribute('validator') !== null && isset($request->getAttribute('validator')['lang'])) ? $request->getAttribute('validator')['lang'] : 'en';
        
        return $handler->handle($request->withAttribute('validator', [
            'valid' => self::lang($lang)->namespace($namespace)->execute($data),
            'errors' => self::getErrors()
        ]));
    }
    
}
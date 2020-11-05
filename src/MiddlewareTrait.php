<?php

namespace HnrAzevedo\Validator;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

trait MiddlewareTrait{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request->withAttribute('validator', [
            'valid' => self::lang($this->requestLang($request))
                           ->namespace($this->requestNamespace($request))
                           ->execute($this->requestData($request)),
            'errors' => self::getErrors()
        ]));
    }

    private function requestData(ServerRequestInterface $request): array
    {
        return ($request->getAttribute('validator') !== null && isset($request->getAttribute('validator')['data'])) ? $request->getAttribute('validator')['data'] : $_REQUEST;
    }

    private function requestNamespace(ServerRequestInterface $request): array
    {
        return ($request->getAttribute('validator') !== null && isset($request->getAttribute('validator')['namespace'])) ? $request->getAttribute('validator')['namespace'] : '';
    }

    private function requestLang(ServerRequestInterface $request): array
    {
        return ($request->getAttribute('validator') !== null && isset($request->getAttribute('validator')['lang'])) ? $request->getAttribute('validator')['lang'] : 'en';
    }
    
}
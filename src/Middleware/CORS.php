<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

class CORS implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === 'OPTIONS') {
            return (new EmptyResponse())
                ->withHeader('Access-control-allow-headers', 'Authorization, Content-type')
                ->withHeader('Access-control-allow-methods', 'GET, POST, PATCH, OPTIONS, HEAD, PUT, DELETE')
                ->withHeader('Access-control-allow-origin', '*');
        }
        return $handler->handle($request)
            ->withHeader('Access-control-allow-origin', '*');
    }
}

<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Middleware;

use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface,
};

/**
 * This is a very crude middleware to serve files in the public/ directory.
 *
 * It utterly fails at:
 * - path traversal attacks
 * - customizable file types
 * - content-type headers
 * - etc
 *
 */
class PublicFileMiddleware implements MiddlewareInterface
{
    private const PERMITTED_EXTENSIONS = [
        'html',
        'css',
        'js',
    ];

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if ($request->getMethod() === 'GET') {
            $uri = $request->getUri();
            $path = $uri->getPath();
            $parts = pathinfo($path);
            if (in_array($parts['extension'] ?? '---', self::PERMITTED_EXTENSIONS)) {
                $target = 'public' . $path;
                if (file_exists($target)) {
                    return new \Zend\Diactoros\Response(
                        fopen($target, 'r'),
                    );
                }
            }
        }
        return $handler->handle($request);
    }
}

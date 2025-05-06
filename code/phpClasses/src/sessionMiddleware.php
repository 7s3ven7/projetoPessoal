<?php

namespace code;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

class sessionMiddleware implements MiddlewareInterface
{

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {

        if(session_status() === PHP_SESSION_NONE)
        {
            if(isset($_COOKIE['token']))
            {

                $_SESSION['token'] = $_COOKIE['token'];

            }

            session_start();

            $_SESSION['session_id_saved'] = session_id();

        }

        if(!isset($_SESSION['last_regeneration']))
        {

            $_SESSION['last_regeneration'] = time();

        }

        if(time() - $_SESSION['last_regeneration'] > 10)
        {

            session_regenerate_id(true);

            $_SESSION['last_regeneration'] = time();

        }


        return $handler->handle($request);

    }

}
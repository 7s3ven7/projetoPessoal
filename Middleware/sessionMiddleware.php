<?php

namespace MyApp\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class sessionMiddleware implements MiddlewareInterface
{

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {

        $this->saveToken();

        $this->setNewIdSession();

        return $handler->handle($request);

    }

    public function sessionStart():void
    {
            session_start();

            $_SESSION['session_id_saved'] = session_id();

    }

    public function saveToken():void
    {

        if(session_status() === PHP_SESSION_NONE)
        {
            if(isset($_COOKIE['token']))
            {

                $_SESSION['token'] = $_COOKIE['token'];

            }

            $this->sessionStart();

        }

    }

    public function setNewIdSession():void
    {

        if(!isset($_SESSION['last_regeneration']))
        {

            session_regenerate_id(true);

            $_SESSION['last_regeneration'] = time();

        }

        if(time() - $_SESSION['last_regeneration'] > 10)
        {

            session_regenerate_id(true);

            $_SESSION['last_regeneration'] = time();

        }

    }

}
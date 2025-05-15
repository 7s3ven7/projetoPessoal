<?php

namespace MyApp\Test\Middleware\TestSession;

require __DIR__ . "/../../vendor/autoload.php";

use MyApp\Middleware\sessionMiddleware;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SessionMiddlewareTest extends TestCase
{

    #[Test]
    public function TestSession():void
    {

        $middleware = new sessionMiddleware;

        $middleware->sessionStart();

        $id = session_id();

        $this->assertNotEmpty($_SESSION['session_id_saved']);

        $middleware->setNewIdSession();

        $idRegenerate = session_id();

        $this->assertNotEquals($id,$idRegenerate);

    }

}
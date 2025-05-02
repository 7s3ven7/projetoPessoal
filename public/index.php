<?php

require_once __DIR__ . '/../vendor/autoload.php';

use code\User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response)
{

    $html =
        '
        <!doctype html>
        <html lang="en">
        <head>
        <meta charset="UTF - 8">
        <meta name="viewport" content="width = device - width, initial - scale = 1">
        <title>Home</title>
        </head>
        <body>
            <table>
                <tr>
                    <td><form action="/create"><button>Criar</button></form></td>
                </tr>
                <tr>
                    <td><form action="/view"><button>Exibir</button></form></td>
                </tr>
            </table>
        </body>
        </html>
     ';

    $response->getBody()->write($html);

    return $response;

});

$app->get('/create', function (Request $request, Response $response)
{

    $html =
        '
        <!doctype html>
        <html lang="en">
        <head>
        <meta charset="UTF - 8">
        <meta name="viewport" content="width = device - width, initial - scale = 1">
        <title>Home</title>
        </head>
            <form action="/create" method="POST">
                <input type="text" name="name" placeholder="Nome" maxlength="254">
                <br>
                <br>
                <input type="password" name="password" placeholder="Senha" required minlength="3" maxlength="254">
                <br>
                <br>
                <input type="email" name="email" placeholder="E-mail" maxlength="254">
                <br>
                <br>
                <input type="text" name="tell" placeholder="47123123123" maxlength="15">
                <br>
                <br>
                <button type="submit">Criar</button>
            </form>
        </html>
     ';

    $response->getBody()->write($html);

    return $response;

});

$app->post('/create', function (Request $request, Response $response)
{

    $nome = trim($_POST['name']) ?? '';
    $tell = trim($_POST['tell']) ?? '';
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    $user = new User($nome, $password, $email, $tell);


    return $response;

});

$app->run();

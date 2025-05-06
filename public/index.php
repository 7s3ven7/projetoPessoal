<?php

require_once __DIR__ . '/../vendor/autoload.php';

use code\User;
use code\sessionMiddleware;

use Slim\Middleware\MethodOverrideMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->add(new sessionMiddleware());

$app->addBodyParsingMiddleware();

$app->add(new MethodOverrideMiddleware());

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
                    <td><form action="/entrar"><button>Entrar</button></form></td>
                </tr>
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

    var_dump($_COOKIE);

    return $response;

});

$app->get('/entrar', function (Request $request, Response $response)
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
            <form action="/entrar" method="POST">
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
                <p>Lembre de mim</p><input type="checkbox" name="remember" value="sim">
                <br>
                <br>
                <button type="submit">Entrar</button>
            </form>
        </html>
     ';

    $response->getBody()->write($html);

    return $response;

});

$app->post('/entrar', function (Request $request, Response $response)
{
    $nome = trim($_POST['name']) ?? '';
    $tell = trim($_POST['tell']) ?? '';
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $remember = $_POST['remember'] ?? '';

    new User(['name' => $nome,'password' => $password,'email' => $email,'tell' => $tell,'remember' => $remember]);

    return $response->withHeader('location', '/')->withStatus(302);

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
                <input type="text" name="name" placeholder="Nome" required maxlength="254">
                <br>
                <br>
                <input type="password" name="password" placeholder="Senha" required minlength="3" maxlength="254">
                <br>
                <br>
                <input type="email" name="email" placeholder="E-mail" required maxlength="254">
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

    $data = $request->getParsedBody();

    $user = new User();

    $user->saveUser(['name' => $data['name'], 'password' => $data['password'], 'email' => $data['email'], 'tell' => $data['tell']]);

    return $response->withHeader('location','/view')->withStatus(302);

});

$app->get('/view', function(Request $request, Response $response)
{

    $user = new User();

    $users = $user->viewUsers();

    $html = '<style>table{border:1px solid black;}td{border:1px solid black;}</style><table><tr><td>id</td><td>nome</td><td>senha</td><td>email</td><td>telefone</td><td>excluir</td><td>modificar</td></tr>';

    foreach($users as $user){

        $html .= '<tr><td>' . htmlspecialchars($user['id']) . '</td><td>' . htmlspecialchars($user['name']) . '</td><td>' . htmlspecialchars($user['password']) . '</td><td>' . htmlspecialchars($user['email']) . '</td><td>' . htmlspecialchars($user['tell']) . '</td><td><form action="/delete/' . htmlspecialchars($user['id']) . '" method="POST" onsubmit="return confirm()"><input type="hidden" name="_METHOD" value="DELETE">
  <button type="submit">Excluir</button>
</form></td><td><form action="/modify/' . htmlspecialchars($user['id']). '" method="POST">
  <button type="submit">modificar</button>
</form></td></tr>';

    }

    $html .= '</table><br><form action="/"><button>Voltar</button></form>';

    $response->getBody()->write($html);

    return $response;

});

$app->delete('/delete/{id}', function (Request $request, Response $response, $args)
{

    $user = new User();

    $user->deleteUser($args['id']);

    return $response->withHeader('location', '/view')->withStatus(302);

});

$app->post('/modify/{id}', function (Request $request, Response $response, $args)
{

    $user = new User();

    $userModify = $user->searchUser($args['id']);

    $html = '
        <!doctype html>
        <html lang="en">
        <head>
        <meta charset="UTF - 8">
        <meta name="viewport" content="width = device - width, initial - scale = 1">
        <title>Home</title>
        </head>
            <form action="/modify/' . $args['id'] . '" method="POST">
                <input type="hidden" name="_METHOD" value="PUT">
                <input type="text" name="name" placeholder="Nome" value="' . $userModify[0]['name'] . '" maxlength="254">
                <br>
                <br>
                <input type="password" name="password" placeholder="Senha" value="' . $userModify[0]['password'] . '" required minlength="3" maxlength="254">
                <br>
                <br>
                <input disabled type="email" placeholder="E-mail" value="' . $userModify[0]['email'] . '" maxlength="254">
                <br>
                <br>
                <input type="text" name="tell" placeholder="47123123123" value="' . $userModify[0]['tell'] . '" maxlength="15">
                <br>
                <br>
                <button type="submit">Salvar</button>
            </form>
        </html>
    ';

    var_dump($_SESSION);

    $response->getBody()->write($html);

    return $response;

});

$app->put('/modify/{id}', function (Request $request, Response $response, $args)
{

    $user = new User();

    $user->modifyUser($args['id'],["name" => $_POST['name'],"password" => password_hash($_POST['password'], PASSWORD_DEFAULT),"tell" => $_POST['tell']]);

    return $response->withHeader('location', '/view')->withStatus(302);

});

$app->run();

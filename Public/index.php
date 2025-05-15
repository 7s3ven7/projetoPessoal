<?php

/*$temp_file = tmpfile();
$meta_data = stream_get_meta_data($temp_file);
$temp_path = $meta_data['uri'];
$stream = fopen($temp_path, 'rb');

return $response
    ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    ->withHeader('Content-Disposition', 'attachment; filename="excel.xlsx"')
    ->withHeader('Cache-Control', 'max-age=0')
    ->withBody(new \Slim\Psr7\Stream($stream));*/

require_once __DIR__ . '/../vendor/autoload.php';

use MyApp\Middleware\sessionMiddleware;
use MyApp\Entity\User;
use MyApp\Service\UserExporter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;

$app = AppFactory::create();

$app->add(new sessionMiddleware());

$app->addBodyParsingMiddleware();

$app->add(new MethodOverrideMiddleware());

$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response)
{

    $html = '<!doctype html>
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
        </html>';

    $response->getBody()->write($html);


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

    $vars = $request->getParsedBody();

    $nome = trim($vars['name']) ?? '';
    $tell = trim($vars['tell']) ?? '';
    $email = filter_var($vars['email'], FILTER_SANITIZE_EMAIL);
    $password = $vars['password'] ?? '';
    $remember = $vars['remember'] ?? 'nao';

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

    $vars = $request->getParsedBody();

    $user = new User();

    $user->saveUser(['name' => $vars['name'], 'password' => $vars['password'], 'email' => $vars['email'], 'tell' => $vars['tell']]);

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

    $sheet = new UserExporter();

    $sheet->saveFileServer();

    $html .= '</table><br><form action="/xlsx/users"><button>baixar</button></form><br><form action="/"><button>Voltar</button></form>';

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

    $vars = $request->getParsedBody();

    $user->modifyUser($args['id'],["name" => $vars['name'],"password" => password_hash($vars['password'], PASSWORD_DEFAULT),"tell" => $vars['tell']]);

    return $response->withHeader('location', '/view')->withStatus(302);

});

$app->get('/xlsx/users', function (Request $request, Response $response)
{
    $temp_file = tmpfile();
    $meta_data = stream_get_meta_data($temp_file);
    $temp_path = $meta_data['uri'];
    $stream = fopen($temp_path, 'rb');

    return $response
        ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->withHeader('Content-Disposition', 'attachment; filename="excel.xlsx"')
        ->withHeader('Cache-Control', 'max-age=0')
        ->withBody(new \Slim\Psr7\Stream($stream))
        ->withHeader('location','/view');

});
$app->run();

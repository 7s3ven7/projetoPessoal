<?php

namespace MyApp\Test\Repository\TestDb;

require __DIR__ . "/../../vendor/autoload.php";

use MyApp\Repository\Db;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PDOException;

class DbTest extends TestCase
{

    #[DataProvider('dataProvider')]
    #[Test]
    public function TestCrud($name, $nameUpdated, $email):void
    {

        $db = new Db();

        $this->assertIsObject($db);

        if($name !== null && $email !== null && $nameUpdated !== null && $name !== '' && $email !== '' && $nameUpdated !== '')
        {

            $db->query("INSERT INTO users (name, password, email, tell) VALUES (:name, :password, :email, :tell)",
                [
                    'name' => $name,
                    'password' => '123123',
                    'email' => $email,
                    'tell' => '123123123'
                ]);

            $selectResult = $db->select("SELECT * FROM users WHERE email = :email", ["email" => $email]);

            $this->assertEquals($name, $selectResult[0]['name'], 'Os dados deviriam ser iguais');
            $this->assertEquals($email, $selectResult[0]['email'], 'Os dados deviriam ser iguais');

            $db->query("UPDATE users SET name = :name WHERE id = :id", ["name" => $nameUpdated, "id" => $selectResult[0]['id']]);

            $selectResult = $db->select("SELECT * FROM users WHERE id = :id", ["id" => $selectResult[0]['id']]);

            $this->assertEquals($nameUpdated, $selectResult[0]['name'], 'Os dados deviriam ser iguais');

            $db->query("DELETE FROM users WHERE id = :id", ['id' => $selectResult[0]['id']]);

            $selectResult = $db->select("SELECT * FROM users WHERE email = :email", ["email" => $email]);

            $this->assertEmpty($selectResult, 'Os dados deveriam ter sido deletados');

        } else
        {

            $this->expectException(PDOException::class);

            $db->query("INSERT INTO users (email) VALUES (:email)",
                [
                    'email' => $email
                ]);

            $selectResult = $db->select("SELECT * FROM users WHERE email = :email", ["email" => $email]);

            $this->assertEmpty($selectResult, 'não deveria ter lido nenhum dado');

            if(!Empty($selectResult))
            {

                $updateResult = $db->query("UPDATE users SET name = :name WHERE id = :id",["name" => $nameUpdated, "id" => $selectResult[0]['id']])->rowCount();

                $this->assertEquals(0, $updateResult, 'Update não deveria alterar algum dado');

                $deleteResult = $db->query("DELETE FROM users WHERE id = :id", ['id' => $selectResult[0]['id']])->rowCount();

                $this->assertEquals(0, $deleteResult, 'O Delete não deveria ter deletado algo');
            }

        }

    }

    public static function dataProvider(): array
    {
        return [
            'ValidName_ValidUpdated_ValidEmail' => ['name' => 'wesley', 'nameUpdated' => 'wesleyUpdate', 'email' => 'teste1@gmail.com'],
            'ValidName_ValidUpdated_NullEmail' => ['name' => 'wesley', 'nameUpdated' => 'wesleyUpdate', 'email' => null],
            'ValidName_ValidUpdated_EmptyEmail' => ['name' => 'wesley', 'nameUpdated' => 'wesleyUpdate', 'email' => ''],
            'NullName_NullUpdated_NullEmail' => ['name' => null, 'nameUpdated' => null, 'email' => null],
            'EmptyName_EmptyUpdated_EmptyEmail' => ['name' => '', 'nameUpdated' => '', 'email' => ''],
        ];
    }


}
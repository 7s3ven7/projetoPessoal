<?php

namespace MyApp\Test\Entity\TestUser;

require __DIR__ . "/../../vendor/autoload.php";

use MyApp\Entity\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use stdClass;

class UserTest extends testCase
{

    public object $user;

    public int $idForDeleteTest;

    /**
     * @throws RandomException
     */

    public function setUp(): void
    {

        $this->user = new User(['name' => 'wesley', 'email' => '7s3ven7.11111@gmail.com', 'emailCreated' => '7sss3ven7sss@gmail.com' ,'password' => '123123', 'tell' => '47999718040', 'remember' => 'nao']);

    }

    public string $verify = '';

    /**
     * @throws RandomException
     */

    #[Test]
    public function testSession(): void
    {

        $this->user->loadUser();

        $this->assertNotEmpty($_SESSION['user']);

        $this->assertNotEmpty($_SESSION['name']);

        $this->assertNotEmpty($_SESSION['email']);

        $this->assertNotEmpty($_SESSION['tell']);

    }

    #[Test]
    public function testGetters(): void
    {

        $password = $this->user->getPassword();

        $this->assertEquals($password, $this->user->password);

        $email = $this->user->getEmail();

        $this->assertEquals($email, $this->user->email);

    }

    #[DataProvider('dataGetValues')]
    #[Test]
    public function testGetValues($email): void
    {

        $emails = $this->user->searchUserByEmail($email);

        if(count($emails) > 0)
        {

            $this->assertEquals($email, $emails[0]['email']);

        }else
        {

            $this->assertEmpty($emails);

        }

    }

    #[Test]
    public function testRead(): void
    {

        $this->assertNotEmpty($this->user->findUser());

        $viewUsersTest = $this->user->viewUsers();

        $this->assertNotEmpty($viewUsersTest);

        $this->assertTrue($this->user->verifyPassword());

        $this->assertTrue($this->user->verifyUser());

    }

    #[DataProvider('dataCreateAndDelete')]
    #[Test]
    public function testCreateAndDelete(array $data): void
    {
        /* Inicio do Create */

        $this->user->saveUser([
            'name' => $data['name'],
            'password' => $data['password'],
            'email' => $data['email'],
            'tell' => $data['tell']
        ]);

        $userSaved = $this->user->searchUserByEmail($data['email']);

        /* Inicio do Delete */

        if(count($userSaved) > 0)
        {

            $this->assertNotEmpty($userSaved[0]);

            $this->idForDeleteTest = $userSaved[0]['id'];

            $searchTest = $this->user->searchUser($this->idForDeleteTest);

            $this->assertNotEmpty($searchTest[0]);

            $this->user->deleteUser($searchTest[0]['id']);

            $deletedUserTest = $this->user->searchUser($searchTest[0]['id']);

            $this->assertEmpty($deletedUserTest);

        }else
        {

            $this->assertEmpty($userSaved);

        }

    }

    #[DataProvider('dataUpdate')]
    #[Test]
    public function testUpdate(array $data): void
    {

        if($this->user->validateUser($data)){

            $userDb = $this->user->searchUserByEmail($data['email']);

            $this->assertTrue($this->user->validateUser($data));

            $this->user->modifyUser($userDb[0]['id'], ['name' => $data['name'], 'password' => '$2y$12$sXxbbFVNuJFqSCqReVEjE.1ZZI34ncXAkvO9dptHHuVBE5T7BxsGW', 'tell' => $data['tell']]);

            $userDb = $this->user->searchUserByEmail($data['email']);

            $this->assertEquals($data['name'], $userDb[0]['name']);

        }else{

            $this->assertNotTrue($this->user->validateUser($data));

        }

    }

    public function tearDown(): void
    {

        $this->user = new stdClass();

    }

    public static function dataGetValues(): array
    {

        return [
            'ValidEmail' => ['email' => '7s3ven7.11111@gmail.com'],
            'InvalidEmail' => ['email' => '7s3ven7.11123123111@gmail.com'],
            'EmptyEmail' => ['email' => ''],
            'NullEmail' => ['email' => null]
        ];

    }

    public static function dataCreateAndDelete(): array
    {

        return [
            'ValidForm' => ['data' => ['name' => 'nameValid', 'password' => 'secretPass', 'email' => 'emailValid@gmail.com', 'tell' => '47999718040']],
            'EmptyForm' => ['data' => ['name' => '', 'password' => '', 'email' => '', 'tell' => '']],
            'NullForm' => ['data' => ['name' => null, 'password' => null, 'email' => null, 'tell' => null]],
        ];

    }

    public static function dataUpdate(): array
    {

        return [
            'ValidForm' => ['data' => ['name' => 'nameValid', 'email' => '7s3ven7.11111@gmail.com', 'tell' => '47999718049']],
            'InvalidForm' => ['data' => ['name' => '', 'email' => '7s3ven7.11111@gmail.com', 'tell' => '']],
            'NullForm' => ['data' => ['name' => null, 'email' => '7s3ven7.11111@gmail.com', 'tell' => null]],
        ];

    }

}

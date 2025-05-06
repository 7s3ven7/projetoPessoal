<?php

namespace code;

use code\dataBase\Db;
use Random\RandomException;

Class User extends Db
{

    private string $password;

    private string $email;

    private string $remember;

    private array $userFound;

    public function __construct($data = []) {

        parent::__construct();



        if ($data === [])
        {

            $this->password = $_SESSION['password'];
            $this->email = $_SESSION['email'];

        } else
        {

            $this->password = $data['password'];
            $this->email = $data['email'];
            $this->remember = $data['remember'];

            if(!$this->verifyUser()) {

                $this->loadUser();

            }

        }



    }

    private function getPassword(): string
    {

        return $this->password;

    }

    private function getEmail(): string
    {

        return $this->email;

    }

    public function saveUser($data):bool
    {

        if(!count($this->searchUserByEmail($data['email']))){

            if(!$this->validateUser($data))
            {
                return false;
            }

            parent::query("INSERT INTO users (name, password, email, tell) VALUES (:name, :password, :email, :tell)",
                [
                    'name' => trim($data['name']),
                    'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                    'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                    'tell' => trim($data['tell'])
                ]);

            return true;

        }

        return false;

    }

    private function findUser():array
    {

        $this->userFound = parent::select("SELECT * FROM users WHERE email = :email", ["email" => $this->getEmail()]);

        return $this->userFound;

    }

    private function verifyUser():bool
    {
        if(!count($this->findUser()))
        {

            return true;

        }
        else{

            return false;

        }

    }

    private function verifyPassword():bool
    {

        $password = $this->userFound[0]['password'];

        if(password_verify($this->getPassword(), $password))
        {

            return true;

        }else
        {

            return false;

        }

    }

    /**
     * @throws RandomException
     */

    private function loadUser():void
    {

        if(isset($_COOKIE['token']))
        {

            $data = parent::select('SELECT * FROM users WHERE token = :token', ['token' => $_COOKIE['token']]);

            $_SESSION['user'] = $data[0]['id'];
            $_SESSION['name'] = $data[0]['name'];
            $_SESSION['password'] = $data[0]['password'];
            $_SESSION['email'] = $data[0]['email'];
            $_SESSION['tell'] = $data[0]['tell'];

        }elseif($this->verifyPassword()){

            $_SESSION['user'] = $this->userFound[0]['id'];
            $_SESSION['name'] = $this->userFound[0]['name'];
            $_SESSION['password'] = $this->userFound[0]['password'];
            $_SESSION['email'] = $this->userFound[0]['email'];
            $_SESSION['tell'] = $this->userFound[0]['tell'];

            if($this->remember === 'sim')
            {
                $sessionToken = bin2hex(random_bytes(32));

                parent::query("UPDATE users SET token = :token WHERE id = :id",['token' => $sessionToken, 'id' => $this->userFound[0]['id']]);

                setcookie('token', $sessionToken , time() + (86400), "/");

            }

        }

    }

    public function viewUsers():array
    {

        return parent::select("SELECT * FROM users",[]);

    }

    public function searchUser($id):array
    {

        return parent::select("SELECT * FROM users WHERE id = :id", ['id' => $id]);

    }

    public function searchUserByEmail($email):array
    {

        return parent::select("SELECT * FROM users WHERE email = :email", ['email' => $email]);

    }

    public function deleteUser($id):void
    {

        parent::query("DELETE FROM users WHERE id = :id", ['id' => $id]);

        var_dump((string)$_SESSION['user']);
        var_dump($id);

        if((string)$id === (string)$_SESSION['user'])
        {

            session_unset();

            session_destroy();

        }

    }

    public function modifyUser($id,$data):void
    {

        parent::query("UPDATE users SET name = :name, password = :password, tell = :tell WHERE id = :id",["name"=>$data['name'],"password"=>$data['password'],"tell"=>$data['tell'],"id"=>$id]);

    }

    private function validateUser($data):bool
    {

        if(empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        {

            return false;

        }

        if(empty($data['tell']) || !preg_match('/^\d{10,15}$/', trim($data['tell'])))
        {

            return false;

        }

        return true;

    }


}

<?php

namespace MyApp\Entity;

use Random\RandomException;
use MyApp\Repository\Db;

Class User extends Db
{

    public string $password;

    public string $email;

    public string $remember;

    public array $userFound;

    /**
     * @throws RandomException
     */

    public function __construct($data = []) {

        parent::__construct();

        if (empty($data))
        {

            if(isset($_SESSION['password'], $_SESSION['email']))
            {

                $this->password = $_SESSION['password'];
                $this->email = $_SESSION['email'];

            }

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

    public function getPassword(): string
    {

        return $this->password;

    }

    public function getEmail(): string
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

    public function findUser():array
    {

        return $this->userFound = parent::select("SELECT * FROM users WHERE email = :email", ["email" => $this->getEmail()]);

    }

    public function verifyUser():bool
    {

        if(count($this->findUser()) > 0)
        {

            return true;

        }
        else{

            return false;

        }

    }

    public function verifyPassword():bool
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

    public function loadUser():void
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

        return parent::select("SELECT id,name,password,email,tell FROM users",[]);

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

    public function validateUser($data):bool
    {

        if(empty($data['name']))
        {

            return false;

        }

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

<?php

namespace code;

use code\dataBase\Db;

Class User extends Db
{

    private string $name;

    private string $password;

    private string $email;

    private string $tell;

    private array $userFound;

    public function __construct(private readonly string $userName, private readonly string $userPassword, private readonly string $userEmail, private readonly string $userTell) {

        parent::__construct();

        $this->name = $this->userName;
        $this->password = $this->userPassword;
        $this->email = $this->userEmail;
        $this->tell = $this->userTell;

        if($this->verifyUser()){

            $this->saveUser();

            return $this->loadUser();

        }else {

            return $this->loadUser();

        }

    }

    public function getName(): string
    {

        return $this->name;

    }

    public function getPassword(): string
    {

        return $this->password;

    }

    public function getEmail(): string
    {

        return $this->email;

    }

    function getTell(): string
    {

        return $this->tell;

    }

    public function saveUser():void
    {

        parent::query("INSERT INTO users (name, password, email, tell) VALUES (:name, :password, :email, :tell)",
            [
                'name' => $this->getName(),
                'password' => password_hash($this->getPassword(), PASSWORD_DEFAULT),
                'email' => $this->getEmail(),
                'tell' => $this->getTell()
            ]);

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

    private function loadUser():array
    {
        if($this->verifyPassword()){

            $data = array(
                $this->userFound[0]['id'],
                $this->userFound[0]['name'],
                '**********',
                $this->userFound[0]['email'],
                $this->userFound[0]['tell']
            );

            return [ 'data' => $data, 'message' => 'logado com sucesso', 'error' => ''];

        }
        else
        {

            return ['message' => 'senha incorreta', 'error' => 'erro 404'];

        }

    }

}

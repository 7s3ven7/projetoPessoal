<?php

namespace code;

use code\dataBase\Db;

Class User extends Db
{

    private string $name;

    private string $password;

    private string $email;

    private string $tell;

    public function __construct(private readonly string $userName, private readonly string $userPassword, private readonly string $userEmail, private readonly string $userTell) {

        parent::__construct();

        $this->name = $this->userName;
        $this->password = $this->userPassword;
        $this->email = $this->userEmail;
        $this->tell = $this->userTell;

        $this->saveUser();

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
                'password' => $this->getPassword(),
                'email' => $this->getEmail(),
                'tell' => $this->getTell()
            ]);

    }

    public function findUser():void
    {

    }

    public function verifyUser():void
    {

        parent::query();

    }

    public function getVar()
    {


    }


}
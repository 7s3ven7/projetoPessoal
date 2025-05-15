<?php

namespace MyApp\Service;

use Myapp\Entity\User;
use MyApp\Entity\Animal;

class UserExporter extends User
{

    public array $dataHead =
        [
            [
                'id',
                'nome',
                'senha',
                'email',
                'telefone'
            ],
            [
                'id',
                'animal',
                'peso'
            ]
        ];

    public array $data;

    public function __construct()
    {

        parent::__construct();

        $animal = new Animal();

        $this->data = [['dataHead' => $this->dataHead[0], 'dataBody' => $this->viewUsers(), 'null' => null, 'startPosition' => 'A2'],['dataHead' => $this->dataHead[1], 'dataBody' => $animal->viewAnimals(), 'null' => null, 'startPosition' => 'A2']];

    }

    public function saveFileServer():void
    {

        $excel = new Excel();

        $excel->write($this->data);

    }

}
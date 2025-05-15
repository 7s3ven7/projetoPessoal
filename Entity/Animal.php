<?php

namespace MyApp\Entity;

use MyApp\Repository\Db;

class Animal extends Db
{

    public function viewAnimals(): array
    {

        return $this->select('SELECT * FROM animals',[]);

    }

}
<?php

namespace MyApp\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel
{

    public function write($data)
    {

        if(count($data) > 1){

            foreach($data as $row => $value){

                var_dump($data);
                var_dump($row);
                var_dump($value);

            }

        }

        $Spreadsheet = new Spreadsheet();

        if(!file_exists('temp/teste.xlsx'))
        {

            $Spreadsheet = IOFactory::load('temp/teste.xlsx');

        }

        $active = $Spreadsheet->getActiveSheet();

        if(count($data) > 1){

            foreach($data as $row => $value){

                $active->setTitle('Sheet'.$row);

                $active->fromArray($value['dataHead'], $value['null'], 'A1');

                $active->fromArray($value['dataBody'], $value['null'], $value['startPosition']);

                var_dump($data);
                echo '<br>';
                var_dump($row);
                echo '<br>';
                var_dump($value);
                echo '<br>';

            }

        }
        $this->save($Spreadsheet);

    }

    public function save($xlsx):void
    {

        $writer = new Xlsx($xlsx);

        $writer->save('temp/teste.xlsx');

    }

}
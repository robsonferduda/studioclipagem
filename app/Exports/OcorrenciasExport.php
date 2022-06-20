<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class OcorrenciasExport implements FromCollection
{
    private $dados;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }
}

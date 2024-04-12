<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OcorrenciasExport implements FromCollection, WithHeadings
{
    private $dados;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array {

        return [
            "Data",
            "TIpo",
            "Título",
            "Sinopse",
            "Veículo",
            "Seção",
            "Cidade",
            "Estado",
            "Link",
            "Rotorno"
        ];
    }

    public function collection()
    {
        return collect($this->data);
    }
}
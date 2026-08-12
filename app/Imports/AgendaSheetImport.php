<?php

namespace App\Imports;

use App\Models\agenda\Agenda;
use App\Models\Kelas\Kelas;
use App\Models\mapel\Mapel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AgendaSheetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari mata pelajaran berdasarkan nama
        $mapel = Mapel::where(
            'nama',
            $row['mata_pelajaran']
        )->first();

        $kelas = Kelas::where(
            'nama',
            $row['kelas']
        )->first();

        // Jika tidak ditemukan, jangan masukkan data
        if (!$mapel) {
            return null;
        }

        return new Agenda([
            'id_mapel' => $mapel->id,
            'id_kelas'          => $kelas->id,
            'time_start'     => $this->convertDate($row['time_start']),
            'time_end'       => $this->convertDate($row['time_end']),
        ]);
    }

    private function convertDate($value)
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)
                ->format('Y-m-d H:i:s');
        }

        return date('Y-m-d H:i:s', strtotime($value));
    }
}
<?php


namespace App\Components\Import;


trait JsonConverter
{

    public function convert($string)
    {
        $data = json_decode($string, true);

        $csvFileName = public_path('storage/temp.csv');
        $fp = fopen($csvFileName, 'w');

        $header = false;
        foreach ($data as $row) {
            if (empty($header)) {
                $header = array_keys($row);
                fputcsv($fp, $header);
                $header = array_flip($header);
            }
            fputcsv($fp, array_merge($header, $row));
        }

        fclose($fp);

        return $csvFileName;
    }
}
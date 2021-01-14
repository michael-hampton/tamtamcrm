<?php


namespace App\Components\Reports;


use App\Components\Pdf\StatementPdf;

class BaseReport
{
    protected array $columns = [];

    protected array $translations = [];

    protected StatementPdf $objPdf;

    public function __construct(array $columns, array $translations, StatementPdf $objPdf)
    {
        $this->columns = $columns;
        $this->translations = $translations;
        $this->objPdf = $objPdf;
    }

    protected function buildTable(array $data, array $table_structure)
    {
        $header = '';
        $table_row = '';

        $labels = $this->objPdf->getLabels();
        $values = $this->objPdf->getValues();

        foreach ($this->columns as $key => $column) {
            $header .= '<td>' . trans($this->translations[$column]) . '</td>';
            $table_row .= '<td class="table_header_td_class">' . $column . '</td>';
        }

        $types = array_keys($table_structure);

        foreach ($types as $type) {
            if (!empty($data[$type])) {
                $table_structure[$type]['header'] .= '<tr>' . strtr($header, $labels) . '</tr>';

                foreach ($data[$type] as $outstanding) {
                    $tmp = strtr($table_row, $outstanding);
                    $tmp = strtr($tmp, $values);

                    $table_structure[$type]['body'] .= '<tr>' . $tmp . '</tr>';
                }
            }
        }

        return $table_structure;
    }
}
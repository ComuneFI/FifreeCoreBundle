<?php

namespace Fi\CoreBundle\DependencyInjection;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class EsportaTabellaXls
{
    public function esportaexcel($parametri = array())
    {
        set_time_limit(960);
        ini_set('memory_limit', '2048M');

        //Creare un nuovo file
        $spreadsheet = new Spreadsheet();  /* ----Spreadsheet object----- */
        $objPHPExcel = new Xls($spreadsheet);  /* ----- Excel (Xls) Object */
        $spreadsheet->setActiveSheetIndex(0);

        // Set properties
        $spreadsheet->getProperties()->setCreator('Comune di Firenze');
        $spreadsheet->getProperties()->setLastModifiedBy('Comune di Firenze');

        $testata = $parametri['testata'];
        $rispostaj = $parametri['griglia'];

        $modellicolonne = $testata['modellocolonne'];

        //Scrittura su file
        $sheet = $spreadsheet->getActiveSheet();
        $titolosheet = 'Esportazione ' . $testata['tabella'];
        $sheet->setTitle(substr($titolosheet, 0, 30));
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Verdana');

        $this->printHeaderXls($modellicolonne, $testata, $sheet);

        $risposta = json_decode($rispostaj);
        if (isset($risposta->rows)) {
            $righe = $risposta->rows;
        } else {
            $righe = array();
        }

        $this->printBodyXls($righe, $modellicolonne, $sheet);

        //Si crea un oggetto
        $todaydate = date('d-m-y');

        $filename = 'Exportazione_' . $testata['tabella'];
        $filename = $filename . '-' . $todaydate . '-' . strtoupper(md5(uniqid(rand(), true)));
        $filename = $filename . '.xls';
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($filename)) {
            unlink($filename);
        }

        $objPHPExcel->save($filename);

        return $filename;
    }
    private function printHeaderXls($modellicolonne, $testata, $sheet)
    {
        $indicecolonnaheader = 0;
        $letteracolonna = 0;
        foreach ($modellicolonne as $modellocolonna) {
            //Si imposta la larghezza delle colonne
            $letteracolonna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($indicecolonnaheader);
            $width = (int) $modellocolonna['width'] / 7;
            $indicecolonnaheadertitle = $testata['nomicolonne'][$indicecolonnaheader];
            $coltitlecalc = isset($indicecolonnaheadertitle) ? $indicecolonnaheadertitle : $modellocolonna['name'];
            $coltitle = strtoupper($coltitlecalc);
            $sheet->setCellValueByColumnAndRow($indicecolonnaheader, 1, $coltitle);
            $sheet->getColumnDimension($letteracolonna)->setWidth($width);

            ++$indicecolonnaheader;
        }

        if ($indicecolonnaheader > 0) {
            //Si imposta il colore dello sfondo delle celle
            //Colore header
            $style_header = array(
                'fill' => array(
                    'type' => Fill::FILL_SOLID,
                    'color' => array('rgb' => 'E5E4E2')
                ),
                'font' => array(
                    'bold' => true
                )
            );
            $sheet->getStyle('A1:' . $letteracolonna . '1')->applyFromArray($style_header);
        }

        $sheet->getRowDimension('1')->setRowHeight(20);
    }
    private function getValueCell($tipocampo, $vettorecella)
    {
        $valore = null;
        switch ($tipocampo) {
            case 'date':
                $d = (int) substr($vettorecella, 0, 2);
                $m = (int) substr($vettorecella, 3, 2);
                $y = (int) substr($vettorecella, 6, 4);
                $t_date = \PhpOffice\PhpSpreadsheet\Shared\Date::formattedPHPToExcel($y, $m, $d);
                $valore = $t_date;
                break;
            case 'boolean':
                $valore = ($vettorecella == 1) ? 'SI' : 'NO';
                break;
            default:
                $valore = $vettorecella;
                break;
        }
        return $valore;
    }
    private function printBodyXls($righe, $modellicolonne, $sheet)
    {
        $row = 2;
        foreach ($righe as $riga) {
            $vettorecelle = $riga->cell;
            $col = 0;
            foreach ($vettorecelle as $vettorecella) {
                if ($vettorecella === '' || $vettorecella === null) {
                    $col = $col + 1;
                    continue;
                }
                $valore = $this->getValueCell($modellicolonne[$col]['tipocampo'], $vettorecella);
                $sheet->setCellValueByColumnAndRow($col, $row, $valore);
                $col = $col + 1;
            }
            $sheet->getRowDimension($row)->setRowHeight(18);
            ++$row;
        }

        $indicecolonna = 0;
        foreach ($modellicolonne as $modellocolonna) {
            $letteracolonna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($indicecolonna);
            switch ($modellocolonna['tipocampo']) {
                case 'text':
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode("@");
                    break;
                case 'string':
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode("@");
                    break;
                case 'integer':
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
                    break;
                case 'float':
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode('#,##0.00');
                    break;
                case 'number':
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode('#,##0.00');
                    break;
                case 'datetime':
                    //\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYYSLASH
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode("dd/mm/yyyy");
                    break;
                case 'date':
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode("dd/mm/yyyy");
                    break;
                default:
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode("@");
                    break;
            }

            ++$indicecolonna;
        }
    }
}

<?php

namespace Fi\CoreBundle\DependencyInjection;

use PHPExcel;
use PHPExcel_CachedObjectStorageFactory;
use PHPExcel_Cell;
use PHPExcel_Settings;
use PHPExcel_Shared_Date;
use PHPExcel_Style_Fill;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Writer_Excel5;

class EsportaTabellaXls
{
    public function esportaexcel($parametri = array())
    {
        set_time_limit(960);
        ini_set('memory_limit', '2048M');

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '8MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        //Creare un nuovo file
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);

        // Set properties
        $objPHPExcel->getProperties()->setCreator('Comune di Firenze');
        $objPHPExcel->getProperties()->setLastModifiedBy('Comune di Firenze');

        $testata = $parametri['testata'];
        $rispostaj = $parametri['griglia'];

        $modellicolonne = $testata['modellocolonne'];

        //Scrittura su file
        $sheet = $objPHPExcel->getActiveSheet();
        $titolosheet = 'Esportazione ' . $testata['tabella'];
        $sheet->setTitle(substr($titolosheet, 0, 30));
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Verdana');

        $this->printHeaderXls($modellicolonne, $testata, $sheet);

        $risposta = \json_decode($rispostaj);
        if (isset($risposta->rows)) {
            $righe = $risposta->rows;
        } else {
            $righe = array();
        }

        $this->printBodyXls($righe, $modellicolonne, $sheet);

        //Si crea un oggetto
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $todaydate = date('d-m-y');

        $filename = 'Exportazione_' . $testata['tabella'];
        $filename = $filename . '-' . $todaydate . '-' . strtoupper(md5(uniqid(rand(), true)));
        $filename = $filename . '.xls';
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($filename)) {
            unlink($filename);
        }

        $objWriter->save($filename);

        return $filename;
    }
    private function printHeaderXls($modellicolonne, $testata, $sheet)
    {
        $indicecolonnaheader = 0;
        $letteracolonna = 0;
        foreach ($modellicolonne as $modellocolonna) {
            //Si imposta la larghezza delle colonne
            $letteracolonna = PHPExcel_Cell::stringFromColumnIndex($indicecolonnaheader);
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
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '0404B4'),
                ),
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                ),
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
                $t_date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);
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
            $letteracolonna = PHPExcel_Cell::stringFromColumnIndex($indicecolonna);
            switch ($modellocolonna['tipocampo']) {
                case 'text':
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
                    break;
                case 'string':
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
                    break;
                case 'integer':
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
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
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode('dd/mm/yyyy');
                    break;
                case 'date':
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode('dd/mm/yyyy');
                    break;
                default:
                    $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
                    break;
            }

            ++$indicecolonna;
        }
    }
}

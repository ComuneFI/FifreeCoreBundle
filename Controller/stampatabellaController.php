<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Fi\CoreBundle\Controller\FiController;
use \TCPDF;

class stampatabellaController extends FiController {

    public function __construct($container = null) {

        if ($container)
            $this->setContainer($container);
    }

    public function stampaCampoSN($parametri = array()) {
        $tabella = $parametri["tabella"];
        $campo = $parametri["campo"];

        return true;
    }

    public function stampa($parametri = array()) {
        $testata = $parametri["testata"];
        $rispostaj = $parametri["griglia"];
        $request = $parametri["request"];
        $nomicolonne = $testata["nomicolonne"];

        $modellicolonne = $testata["modellocolonne"];
        $larghezzaform = 900;

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        //echo PDF_HEADER_LOGO;

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'FiFree2', isset($testata["titolo"]) && ($testata["titolo"] != "") ? $testata["titolo"] : "Elenco " . $request->get("nometabella"), array(0, 0, 0), array(0, 0, 0));
        $pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetFillColor(220, 220, 220);

        $pdf->AddPage("L");
        $h = 6;
        $border = 1;
        $ln = 0;
        $align = "L";
        $fill = 0;

        $dimensions = $pdf->getPageDimensions();

        $this->stampaTestata($pdf, $nomicolonne, $modellicolonne, $larghezzaform, $h, $border, $align, $fill, $ln);

        // Dati
        $risposta = json_decode($rispostaj);
        $righe = $risposta->rows;
        $pdf->SetFont('helvetica', '', 9);
        foreach ($righe as $riga) {
            $fill = !$fill;
            $vettorecelle = $riga->cell;

            $arr_heights = array();
            foreach ($vettorecelle as $posizione => $valore) {
                if (!is_object($valore)) {
                    if (isset($modellicolonne[$posizione])) {
                        $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform) / 2;
                    } else {
                        $width = ((297 * 100) / $larghezzaform) / 2;
                    }
                    $arr_heights[] = $pdf->getNumLines($valore, $width);
                }
            }
            //work out the number of lines required
            $rowcount = max($arr_heights);
            $startY = $pdf->GetY();
            if (($startY + $rowcount * $h) + $dimensions['bm'] > ($dimensions['hk'])) {
                // page break
                $pdf->AddPage("L");
                // stampa testata
                $this->stampaTestata($pdf, $nomicolonne, $modellicolonne, $larghezzaform, $h, $border, $align, $fill, $ln);
            }
            //now draw it
            foreach ($vettorecelle as $posizione => $valore) {
                if (!is_object($valore)) {
                    if (isset($modellicolonne[$posizione])) {
                        $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform) / 2;
                    } else {
                        $width = ((297 * 100) / $larghezzaform) / 2;
                    }
                    $pdf->MultiCell($width, $rowcount * $h, $valore, $border, $align, $fill, $ln);
                }
            }
            $pdf->Ln();
        }

        $pdf->Cell(0, 10, griglia::traduciFiltri(array("filtri" => $risposta->filtri)), 0, false, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Output($request->get("nometabella") . '.pdf', 'I');
        exit;
    }

    public function esportaexcel($parametri = array()) {
        set_time_limit(960);
        ini_set("memory_limit", "2048M");

        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array("memoryCacheSize" => '8MB');
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        //Creare un nuovo file
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);

        // Set properties
        $objPHPExcel->getProperties()->setCreator("Comune di Firenze");
        $objPHPExcel->getProperties()->setLastModifiedBy("Comune di Firenze");

        $testata = $parametri["testata"];
        $rispostaj = $parametri["griglia"];
        $request = $parametri["request"];
        $nomicolonne = $testata["nomicolonne"];

        $modellicolonne = $testata["modellocolonne"];

        //Scrittura su file
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Esportazione " . $testata["tabella"]);
        $sheet->getDefaultStyle()->getFont()->setName('Verdana');
        $indicecolonna = 0;
        foreach ($modellicolonne as $modellocolonna) {
            //Si imposta la larghezza delle colonne
            $letteracolonna = \PHPExcel_Cell::stringFromColumnIndex($indicecolonna);
            $width = (int) $modellocolonna["width"] / 10;
            $coltitle = strtoupper(isset($testata["nomicolonne"][$indicecolonna])?$testata["nomicolonne"][$indicecolonna]:$modellocolonna["name"]);
            $sheet->setCellValueByColumnAndRow($indicecolonna, 1, $coltitle); /**/
            $sheet->getColumnDimension($letteracolonna)->setWidth($width);

            $indicecolonna++;
        }

        if ($indicecolonna > 0) {
            //Si imposta il colore dello sfondo delle celle
            //Colore header
            $style_header = array(
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '0404B4'),
                ),
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                )
            );
            $sheet->getStyle('A1:' . $letteracolonna . '1')->applyFromArray($style_header);
        }

        $sheet->getRowDimension('1')->setRowHeight(20);


        $risposta = json_decode($rispostaj);
        $righe = $risposta->rows;

        $row = 2;
        foreach ($righe as $riga) {
            $vettorecelle = $riga->cell;
            $col = 0;
            foreach ($vettorecelle as $vettorecella) {

                $sheet->setCellValueByColumnAndRow($col, $row, $vettorecella);
                $col = $col + 1;
            }
            $sheet->getRowDimension($row)->setRowHeight(18);
            $row++;
        }

        $indicecolonna = 0;
        foreach ($modellicolonne as $modellocolonna) {
            $letteracolonna = \PHPExcel_Cell::stringFromColumnIndex($indicecolonna);
            if (($modellocolonna["tipocampo"] == "text") || ($modellocolonna["tipocampo"] == "string")) {
                $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                        ->getNumberFormat()
                        ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
            }

            if (($modellocolonna["tipocampo"] == "float") || ($modellocolonna["tipocampo"] == "integer") || ($modellocolonna["tipocampo"] == "number")) {
                $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                        ->getNumberFormat()
                        ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            }

            if (($modellocolonna["tipocampo"] == "datetime") || ($modellocolonna["tipocampo"] == "date")) {
                \PHPExcel_Cell::setValueBinder(new \PHPExcel_Cell_DefaultValueBinder());
                $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                        ->getNumberFormat()
                        ->setFormatCode('dd/mm/yyyy');
            }

            if (($modellocolonna["tipocampo"] == "float") || ($modellocolonna["tipocampo"] == "number")) {
                $sheet->getStyle($letteracolonna . '2:' . $letteracolonna . $row)
                        ->getNumberFormat()
                        ->setFormatCode("#,##0.00");
            }
            $indicecolonna++;
        }

        //Si crea un oggetto
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $todaydate = date("d-m-y");

        //$todaydate = $todaydate . '-' . date("H-i-s");
        $filename = "Exportazione_" . $testata["tabella"];
        $filename = $filename . '-' . $todaydate . "-" . strtoupper(md5(uniqid(rand(), true)));
        $filename = $filename . '.xls';
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($filename)) {
            unlink($filename);
        }

        $objWriter->save($filename);

        return $filename;

        // Dati
        $risposta = json_decode($rispostaj);
        $righe = $risposta->rows;
        foreach ($righe as $riga) {
            $fill = !$fill;
            $vettorecelle = $riga->cell;

            $arr_heights = array();
            foreach ($vettorecelle as $posizione => $valore) {
                if (!is_object($valore)) {
                    if (isset($modellicolonne[$posizione])) {
                        $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform) / 2;
                    } else {
                        $width = ((297 * 100) / $larghezzaform) / 2;
                    }
                    $arr_heights[] = $pdf->getNumLines($valore, $width);
                }
            }
            //work out the number of lines required
            $rowcount = max($arr_heights);
            $startY = $pdf->GetY();
            if (($startY + $rowcount * $h) + $dimensions['bm'] > ($dimensions['hk'])) {
                // page break
                $pdf->AddPage("L");
                // stampa testata
                $this->stampaTestata($pdf, $nomicolonne, $modellicolonne, $larghezzaform, $h, $border, $align, $fill, $ln);
            }
            //now draw it
            foreach ($vettorecelle as $posizione => $valore) {
                if (!is_object($valore)) {
                    if (isset($modellicolonne[$posizione])) {
                        $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform) / 2;
                    } else {
                        $width = ((297 * 100) / $larghezzaform) / 2;
                    }
                    $pdf->MultiCell($width, $rowcount * $h, $valore, $border, $align, $fill, $ln);
                }
            }
            $pdf->Ln();
        }

        $pdf->Cell(0, 10, griglia::traduciFiltri(array("filtri" => $risposta->filtri)), 0, false, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Output($request->get("nometabella") . '.pdf', 'I');
        exit;
    }

    private function stampaTestata($pdf, $nomicolonne, $modellicolonne, $larghezzaform, $h, $border, $align, $fill, $ln) {
        // Testata
        $pdf->SetFont('helvetica', 'B', 9);
        $arr_heights = array();
        foreach ($nomicolonne as $posizione => $nomecolonna) {
            if (isset($modellicolonne[$posizione])) {
                $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform) / 2;
            } else {
                $width = ((297 * 100) / $larghezzaform) / 2;
            }
            $arr_heights[] = $pdf->getNumLines($nomecolonna, $width, FALSE, TRUE, '', 1);
        }
        //work out the number of lines required
        $rowcount = max($arr_heights);
        //now draw it
        foreach ($nomicolonne as $posizione => $nomecolonna) {
            if (isset($modellicolonne[$posizione])) {
                $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform) / 2;
            } else {
                $width = ((297 * 100) / $larghezzaform) / 2;
            }
            $pdf->MultiCell($width, $rowcount * $h, $nomecolonna, $border, $align, $fill, $ln);
        }
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
    }

    /*
      public function stampa($parametri = array()) {
      $testata = $parametri["testata"];
      $rispostaj = $parametri["griglia"];
      $request = $parametri["request"];
      $nomicolonne = $testata["nomicolonne"];

      $modellicolonne = $testata["modellocolonne"];
      $larghezzaform = 900;

      $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

      //echo PDF_HEADER_LOGO;

      $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'FiFree2', isset($testata["titolo"]) && ($testata["titolo"] != "") ? $testata["titolo"] : "Elenco " . $request->get("nometabella"), array(0, 0, 0), array(0, 0, 0));
      $pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));

      $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
      $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

      $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
      $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
      $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

      $pdf->AddPage("L");
      $h = 6;
      $border = 1;
      $noborder = 0;
      $ln = 0;
      $align = "L";
      $fill = 0;

      $pdf->SetFont('helvetica', 'B', 9);
      $pdf->SetFillColor(220, 220, 220);

      //stampa la testata
      $maxnocells = 0;
      $cellcount = 0;
      $startX = $pdf->GetX();
      $startY = $pdf->GetY();
      $startPage = $pdf->GetPage();
      // print text in cells without borders
      foreach ($nomicolonne as $posizione => $nomecolonna) {
      if (isset($modellicolonne[$posizione])) {
      $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform);
      } else {
      $width = ((297 * 100) / $larghezzaform);
      }
      //$pdf->Cell($width / 2, $h, $nomecolonna, $border, $ln, $align, $fill);
      //$pdf->MultiCell($width / 2, $h, $nomecolonna, $border, $align, $fill, $ln);
      $cellcount = $pdf->MultiCell($width / 2, $h, $nomecolonna, $noborder, $align, $fill, $ln);
      if ($cellcount > $maxnocells) {
      $maxnocells = $cellcount;
      }
      }
      if ($pdf->GetPage()!= $startPage) {
      $pdf->SetPage($startPage);
      }
      $pdf->SetXY($startX,$startY);
      //now do borders and fill
      foreach ($nomicolonne as $posizione => $nomecolonna) {
      if (isset($modellicolonne[$posizione])) {
      $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform);
      } else {
      $width = ((297 * 100) / $larghezzaform);
      }
      $pdf->MultiCell($width / 2, $maxnocells * $h, '', $border, $align, $fill, $ln);
      }
      $pdf->Ln();

      $risposta = json_decode($rispostaj);
      $righe = $risposta->rows;

      $pdf->SetFont('helvetica', '', 9);
      foreach ($righe as $riga) {
      $fill = !$fill;
      $vettorecelle = $riga->cell;

      $maxnocells = 0;
      $cellcount = 0;
      $startX = $pdf->GetX();
      $startY = $pdf->GetY();
      $startPage = $pdf->GetPage();
      // print text in cells without borders
      foreach ($vettorecelle as $posizione => $valore) {
      if (!is_object($valore)) {
      if (isset($modellicolonne[$posizione])) {
      $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform);
      } else {
      $width = ((297 * 100) / $larghezzaform);
      }
      //$pdf->Cell($width / 2, $h, $valore, $border, $ln, $align, $fill);
      //$pdf->MultiCell($width / 2, $h, $valore, $border, $align, $fill, $ln);
      $cellcount = $pdf->MultiCell($width / 2, $h, $valore, $noborder, $align, $fill, $ln);
      if ($cellcount > $maxnocells) {
      $maxnocells = $cellcount;
      }
      }
      }
      if ($pdf->GetPage()!= $startPage) {
      $pdf->SetPage($startPage);
      }
      $pdf->SetXY($startX,$startY);
      //now do borders and fill
      foreach ($vettorecelle as $posizione => $valore) {
      if (!is_object($valore)) {
      if (isset($modellicolonne[$posizione])) {
      $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform);
      } else {
      $width = ((297 * 100) / $larghezzaform);
      }
      $pdf->MultiCell($width / 2, $maxnocells * $h, '', $border, $align, $fill, $ln);
      }
      }
      $pdf->Ln();
      }

      $pdf->Cell(0, 10, griglia::traduciFiltri(array("filtri" => $risposta->filtri)), 0, false, 'L', 0, '', 0, false, 'T', 'M');

      $pdf->Output($request->get("nometabella") . '.pdf', 'I');
      exit;
      }

     */
}

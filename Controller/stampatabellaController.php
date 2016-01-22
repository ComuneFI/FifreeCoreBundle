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

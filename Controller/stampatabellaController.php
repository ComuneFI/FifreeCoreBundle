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

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'FiFree2', "Elenco " . $request->get("nometabella"), array(0, 0, 0), array(0, 0, 0));
    $pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


    $pdf->AddPage("L");
    $h = 6;
    $border = 1;
    $ln = 0;
    $align = "L";
    $fill = 0;

    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(220, 220, 220);

    //$pdf->Cell(200, $h, "Elenco " . $request->get("nometabella"), 0, $ln, $align, $fill);
    //$pdf->Ln();
    //stampa la testata 
    foreach ($nomicolonne as $posizione => $nomecolonna) {
      if (isset($modellicolonne[$posizione]["width"]) && ($this->stampaCampoSN(array("tabella" => $request->get("nometabella"), "campo" => $nomecolonna)))) {
        $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform);
        $pdf->Cell($width / 2, $h, $nomecolonna, $border, $ln, $align, $fill);
      }
    }
    $pdf->Ln();

    $risposta = json_decode($rispostaj);

    $righe = $risposta->rows;


    $pdf->SetFont('helvetica', '', 9);
    foreach ($righe as $riga) {
      $fill = !$fill;
      $vettorecelle = $riga->cell;

      foreach ($vettorecelle as $posizione => $valore) {
        if (isset($modellicolonne[$posizione]["width"]) && ($this->stampaCampoSN(array("tabella" => $request->get("nometabella"), "campo" => $nomecolonna)))) {
          $width = ((297 * $modellicolonne[$posizione]["width"]) / $larghezzaform);
          $pdf->Cell($width / 2, $h, $valore, $border, $ln, $align, $fill);
        }
      }
      $pdf->Ln();
    }


    $pdf->Cell(0, 10, griglia::traduciFiltri(array("filtri" => $risposta->filtri)), 0, false, 'L', 0, '', 0, false, 'T', 'M');


    $pdf->Output($request->get("nometabella") . '.pdf', 'I');
  }

}


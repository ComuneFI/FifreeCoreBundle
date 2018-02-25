<?php

namespace Fi\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FiCoreController extends FiController
{

    public function stampatabellaAction(Request $request)
    {
        $this->setup($request);
        $stampaservice = $this->get("ficorebundle.tabelle.stampa.pdf");
        
        $parametri = $this->prepareOutput($request);
        
        $stampaservice->stampa($parametri);

        return new Response('OK');
    }

    public function esportaexcelAction(Request $request)
    {
        $this->setup($request);
        $stampaservice = $this->get("ficorebundle.tabelle.stampa.xls");
        
        $parametri = $this->prepareOutput($request);

        $fileexcel = $stampaservice->esportaexcel($parametri);

        $response = new Response();

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . basename($fileexcel) . '"');

        $response->setContent(file_get_contents($fileexcel));
        
        @unlink($fileexcel);

        return $response;
    }

    public function prepareOutput($request)
    {
        $namespace = $this->getNamespace();
        $bundle = $this->getBundle();
        $container = $this->container;

        $nomebundle = $namespace . $bundle . 'Bundle';

        $em = $this->getDoctrine()->getManager();

        $paricevuti = array(
            'nomebundle' => $nomebundle,
            'nometabella' => $request->get('nometabella'),
            'container' => $container,
            'request' => $request,
        );

        $parametripertestatagriglia = $this->getParametersTestataPerGriglia($request, $container, $em, $paricevuti);

        $testatagriglia = Griglia::testataPerGriglia($parametripertestatagriglia);

        if ($request->get('titolo')) {
            $testatagriglia['titolo'] = $request->get('titolo');
        }
        $parametridatipergriglia = $this->getParametersDatiPerGriglia($request, $container, $em, $paricevuti);
        $corpogriglia = Griglia::datiPerGriglia($parametridatipergriglia);

        $parametri = array('request' => $request, 'testata' => $testatagriglia, 'griglia' => $corpogriglia);
        return $parametri;
    }

    /*public function importaexcelAction(Request $request)
    {
        $this->setup($request);
        $return = "OK";
        try {
            $em = $this->getDoctrine()->getManager();
            $file = $request->files->get('file');
            $tablenamefile = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file->getClientOriginalName());
            $parametri = json_decode($request->get("parametrigriglia"));
            $bundle = $parametri->nomebundle;
            $controller = $parametri->nometabella;
            $repo = $em->getRepository($bundle . ":" . $tablenamefile);
            $className = $repo->getClassName();

            $classentitypath = "\\" . $className;
            if (strtolower($controller) != strtolower($tablenamefile)) {
                $response = new Response("Si sta cercando di caricare i dati di " . $tablenamefile . " in " . $controller);
                return $response;
            }

            if (is_a($repo, "Doctrine\ORM\EntityRepository")) {
                $entitycolumns = $em->getClassMetadata($bundle . ":" . $tablenamefile);
                $objPHPExcel = \PHPExcel_IOFactory::load($file);
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
                    $colonne = array();
                    for ($index = 0; $index < $highestColumnIndex; $index++) {
                        $columnname = strtolower($worksheet->getCellByColumnAndRow($index, 1)->getValue());
                        if (!$entitycolumns->hasField($columnname) || $columnname == "id") {
                            //the entity does not have a such property $columnname or id column
                            continue;
                        } else {
                            $colonne[] = array("colxls" => $index, "name" => $columnname, "type" => $entitycolumns->getTypeOfField($columnname));
                        }
                    }

                    for ($rows = 2; $rows < $highestRow + 1; $rows++) {
                        $newentity = new $classentitypath();
                        foreach ($colonne as $colonna) {
                            $cols = $colonna["colxls"];
                            $valore = $worksheet->getCellByColumnAndRow($cols, $rows)->getValue();
                            if ($valore) {
                                $fieldset = "set" . ucfirst($colonna["name"]);
                                $valore = $this->getValoreCampoExcel($valore, $colonna);
                                $newentity->$fieldset($valore);
                            }
                        }
                        $em->persist($newentity);
                    }
                    $em->flush();
                }
            } else {
                $response = new Response($return);
                return $response;
            }
        } catch (\Exception $exc) {
            $response = new Response($exc->getMessage());
            return $response;
        }

        $response = new Response($return);
        return $response;
    }

    private function getValoreCampoExcel($valoreexcel, $colonna)
    {
        $valore = $valoreexcel;
        if ($colonna["type"] === "date") {
            $exceldata = \PHPExcel_Style_NumberFormat::toFormattedString($valoreexcel, 'YYYY-MM-DD');
            $nuovadata = \DateTime::createFromFormat('Y-m-d', $exceldata);
            $valore = $nuovadata;
        } elseif ($colonna["type"] === "boolean") {
            $valore = (($valoreexcel == 'SI') ? true : false);
        }

        return $valore;
    }*/
}

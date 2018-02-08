<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class TabelleUtility
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    private function listacampitabelladettagli($nometabella, $escludiid, $colonne)
    {
        $risposta = array();
        if ($escludiid == 1) {
            $gestionepermessi = $this->container->get("ficorebundle.gestionepermessi");
            $operatore = $gestionepermessi->utentecorrente();
            foreach ($colonne as $colonna) {
                $nomecampo = trim(strtolower($colonna));
                if (($nomecampo !== 'id') && (strpos($colonna, '_id') === false)) {
                    $qb = $this->container->get("doctrine")->getRepository("FiCoreBundle:Tabelle")
                            ->createQueryBuilder('t')
                            ->where('LOWER(t.nometabella) = :nometabella')
                            ->andWhere('LOWER(t.nomecampo) = :nomecampo')
                            ->andWhere('t.operatori_id = :operatori_id')
                            ->setParameter('nometabella', $nometabella)
                            ->setParameter('nomecampo', $nomecampo)
                            ->setParameter('operatori_id', $operatore['id'])
                            ->getQuery();
                    $labeltrovata = $qb->getResult();
                    if (!$labeltrovata) {
                        $qb = $this->container->get("doctrine")->getRepository("FiCoreBundle:Tabelle")
                                ->createQueryBuilder('t')
                                ->where('LOWER(t.nometabella) = :nometabella')
                                ->andWhere('LOWER(t.nomecampo) = :nomecampo')
                                ->andWhere('t.operatori_id IS NULL')
                                ->setParameter('nometabella', $nometabella)
                                ->setParameter('nomecampo', $nomecampo)
                                ->getQuery();
                        $labeltrovata = $qb->getResult();
                        if (!$labeltrovata) {
                            $risposta[$colonna] = $colonna;
                        } else {
                            if (($labeltrovata[0]->getEtichettaindex()) && ($labeltrovata[0]->getEtichettaindex() != '')) {
                                $risposta[$colonna] = trim($labeltrovata[0]->getEtichettaindex());
                            } else {
                                $risposta[$colonna] = $colonna;
                            }
                        }
                    } else {
                        if (($labeltrovata[0]->getEtichettaindex()) && ($labeltrovata[0]->getEtichettaindex() != '')) {
                            $risposta[$colonna] = trim($labeltrovata[0]->getEtichettaindex());
                        } else {
                            $risposta[$colonna] = $colonna;
                        }
                    }
                }
            }
        } else {
            foreach ($colonne as $colonna) {
                $risposta[$colonna] = $colonna;
            }
        }
        return $risposta;
    }

    public function getListacampitabella($parametri)
    {
        $nometabella = $parametri['nometabella'];
        $escludiid = $parametri['escludiid'];
        $em = $this->container->get("doctrine")->getManager();
        $tableClassName = "";
        $entityClass = "";
        $bundles = $this->container->get('kernel')->getBundles();
        foreach ($bundles as $bundle) {
            $className = get_class($bundle);
            $entityClass = substr($className, 0, strrpos($className, '\\'));
            $tableClassName = '\\' . $entityClass . '\\Entity\\' . $nometabella;
            if (!class_exists($tableClassName)) {
                $tableClassName = '';
                continue;
            } else {
                break;
            }
        }

        if (!$tableClassName) {
            throw new \Exception('Entity per la tabella ' . $nometabella . ' non trovata', '-1');
        }

        if (!$entityClass) {
            throw new \Exception('Entity class per la tabella ' . $nometabella . ' non trovata', '-1');
        }

        $bundleClass = str_replace('\\', '', $entityClass);
        $c = $em->getClassMetadata($bundleClass . ':' . $nometabella);
        $colonne = $c->getColumnNames();
        $risposta = $this->listacampitabelladettagli($nometabella, $escludiid, $colonne);
        //natcasesort($risposta);
        asort($risposta, SORT_NATURAL | SORT_FLAG_CASE);
        return $risposta;
    }
}

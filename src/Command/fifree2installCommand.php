<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class fifree2installCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('fifree2:install')
                ->setDescription('Installazione ambiente fifree')
                ->setHelp('Crea il database, un utente amministratore e i dati di default')
                ->addArgument('admin', InputArgument::REQUIRED, 'Username per amministratore')
                ->addArgument('adminpass', InputArgument::REQUIRED, 'Password per amministratore')
                ->addArgument('adminemail', InputArgument::REQUIRED, 'Email per amministratore')
        //->addOption('yell', null, InputOption::VALUE_NONE, 'Se impostato, urlerà in lettere maiuscole')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $admin = $input->getArgument('admin');
        $adminpass = $input->getArgument('adminpass');
        $adminemail = $input->getArgument('adminemail');

        if (!$admin) {
            echo "Inserire il nome utente dell'amministratore";
            return 1;
        }
        if (!$adminpass) {
            echo "Inserire la password per dell'amministratore";
            return 1;
        }
        if (!$adminemail) {
            echo "Inserire la mail dell'amministratore";
            return 1;
        }

        $commanddb = $this->getApplication()->find('fifree2:createdatabase');
        $argumentsdb = array('command' => 'fifree2:createdatabase');
        $inputc = new ArrayInput($argumentsdb);
        $commanddb->run($inputc, $output);

        $commandschema = $this->getApplication()->find('doctrine:schema:create');
        $argumentsc = array('');
        $inputsc = new ArrayInput($argumentsc);
        $commandschema->run($inputsc, $output);

        $commandfos = $this->getApplication()->find('fos:user:create');
        $argumentsfos = array('command' => 'fos:user:create', '--super-admin' => true, 'username' => $admin, 'email' => $adminemail, 'password' => $adminpass);
        $inputfos = new ArrayInput($argumentsfos);
        $commandfos->run($inputfos, $output);

        $this->loadDefaultValues($admin);
    }

    private function loadDefaultValues($admin) {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $ruolos = new \Fi\CoreBundle\Entity\ruoli();
        $ruolos->setRuolo('Super Admin');
        $ruolos->setPaginainiziale('/adminpanel');
        $ruolos->setIsSuperadmin(true);
        $ruolos->setIsAdmin(true);
        $ruolos->setIsUser(false);
        $em->persist($ruolos);
        $ruoloa = new \Fi\CoreBundle\Entity\ruoli();
        $ruoloa->setRuolo('Amministratore');
        $ruoloa->setPaginainiziale('/adminpanel');
        $ruoloa->setIsSuperadmin(false);
        $ruoloa->setIsAdmin(true);
        $ruoloa->setIsUser(false);
        $em->persist($ruoloa);
        $ruolou = new \Fi\CoreBundle\Entity\ruoli();
        $ruolou->setRuolo('Utente');
        $ruolou->setPaginainiziale('/ffprincipale');
        $ruolou->setIsSuperadmin(false);
        $ruolou->setIsAdmin(false);
        $ruolou->setIsUser(true);
        $em->persist($ruolou);
        $em->flush();

        //Si tiene in memoria l'id del super admin
        $ruolo = $em->getRepository('FiCoreBundle:Ruoli')->findOneBy(array('is_superadmin' => true)); //SuperAdmin
        $operatore = $em->getRepository('FiCoreBundle:Operatori')->findOneByUsername($admin);
        $operatore->setRuoli($ruolo);
        $em->persist($operatore);
        $em->flush();

        $this->insertDefaultMenu($em);

        $permessimnuapp = new \Fi\CoreBundle\Entity\permessi();
        $permessimnuapp->setRuoli($ruolo);
        $permessimnuapp->setModulo('MenuApplicazione');
        $permessimnuapp->setCrud('crud');
        $em->persist($permessimnuapp);

        $permessiopt = new \Fi\CoreBundle\Entity\permessi();
        $permessiopt->setRuoli($ruolo);
        $permessiopt->setModulo('OpzioniTabella');
        $permessiopt->setCrud('crud');
        $em->persist($permessiopt);

        $permessitbl = new \Fi\CoreBundle\Entity\permessi();
        $permessitbl->setRuoli($ruolo);
        $permessitbl->setModulo('Tabelle');
        $permessitbl->setCrud('crud');
        $em->persist($permessitbl);

        $permessi = new \Fi\CoreBundle\Entity\permessi();
        $permessi->setRuoli($ruolo);
        $permessi->setModulo('Permessi');
        $permessi->setCrud('crud');
        $em->persist($permessi);

        $permessiope = new \Fi\CoreBundle\Entity\permessi();
        $permessiope->setRuoli($ruolo);
        $permessiope->setModulo('Operatori');
        $permessiope->setCrud('cru');
        $em->persist($permessiope);

        $permessiruo = new \Fi\CoreBundle\Entity\permessi();
        $permessiruo->setRuoli($ruolo);
        $permessiruo->setModulo('Ruoli');
        $permessiruo->setCrud('crud');
        $em->persist($permessiruo);

        $permessifp = new \Fi\CoreBundle\Entity\permessi();
        $permessifp->setRuoli($ruolo);
        $permessifp->setModulo('Ffprincipale');
        $permessifp->setCrud('crud');
        $em->persist($permessifp);

        $permessifs = new \Fi\CoreBundle\Entity\permessi();
        $permessifs->setRuoli($ruolo);
        $permessifs->setModulo('Ffsecondaria');
        $permessifs->setCrud('crud');
        $em->persist($permessifs);

        $ffprincipalerow = new \Fi\CoreBundle\Entity\ffprincipale();
        $ffprincipalerow->setDescrizione('Descrizione primo record');
        $em->persist($ffprincipalerow);
        $em->flush();
        $ffsecondariarow1 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow1->setFfprincipale($ffprincipalerow);
        $ffsecondariarow1->setDescsec('1° secondaria legato al 1° record principale');
        $ffsecondariarow1->setData(\DateTime::createFromFormat("Y-m-d", date("Y-m-d")));
        $ffsecondariarow1->setIntero(10);
        $ffsecondariarow1->setImporto(12.34);
        $ffsecondariarow1->setNota("Super Nota ffsecondaria");
        $ffsecondariarow1->setAttivo(true);
        $em->persist($ffsecondariarow1);

        $ffsecondariarow2 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow2->setFfprincipale($ffprincipalerow);
        $ffsecondariarow2->setDescsec('2° secondaria legato al 1° record principale');
        $ffsecondariarow2->setData(\DateTime::createFromFormat("Y-m-d", date("Y-m-d")));
        $ffsecondariarow2->setIntero(1);
        $ffsecondariarow2->setImporto(1.23);
        $ffsecondariarow2->setNota("Nota ffsecondaria");
        $ffsecondariarow2->setAttivo(true);


        $em->persist($ffsecondariarow2);

        $ffprincipale = new \Fi\CoreBundle\Entity\ffprincipale();
        $ffprincipale->setDescrizione('Descrizione secondo record');
        $em->persist($ffprincipale);

        $ffsecondaria = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondaria->setFfprincipale($ffprincipale);
        $ffsecondaria->setDescsec('3° secondaria legato al 2° record principale');
        $ffsecondaria->setData(\DateTime::createFromFormat("Y-m-d", date("Y-m-d")));
        $ffsecondaria->setIntero(1000);
        $ffsecondaria->setImporto(1000.12);
        $ffsecondaria->setNota("Nota altra ffsecondaria");
        $ffsecondaria->setAttivo(false);
        $em->persist($ffsecondaria);
        

        $tabelle = new \Fi\CoreBundle\Entity\tabelle();
        $tabelle->setNometabella('*');
        $em->persist($tabelle);

        $opzionitabelle = new \Fi\CoreBundle\Entity\opzioniTabella();
        $opzionitabelle->setTabelle($tabelle);
        $opzionitabelle->setParametro('titolo');
        $opzionitabelle->setValore('Elenco dati per %tabella%');
        $em->persist($opzionitabelle);

        $opzionitabelleag = new \Fi\CoreBundle\Entity\opzioniTabella();
        $opzionitabelleag->setTabelle($tabelle);
        $opzionitabelleag->setDescrizione('Altezza Griglia');
        $opzionitabelleag->setParametro('altezzagriglia');
        $opzionitabelleag->setValore(400);
        $em->persist($opzionitabelleag);

        $em->flush();
    }

    private function insertDefaultMenu($em) {
        $menutabelle = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menutabelle->setNome('Tabelle');
        $menutabelle->setAttivo(true);
        $menutabelle->setOrdine(10);
        $em->persist($menutabelle);
        $em->flush();

        $menufp = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menufp->setPadre($menutabelle->getId());
        $menufp->setNome('FFprincipale');
        $menufp->setPercorso('Ffprincipale');
        $menufp->setAttivo(true);
        $menufp->setOrdine(10);
        $em->persist($menufp);

        $menufs = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menufs->setPadre($menutabelle->getId());
        $menufs->setNome('FFsecondaria');
        $menufs->setPercorso('Ffsecondaria');
        $menufs->setAttivo(true);
        $menufs->setOrdine(10);
        $em->persist($menufs);
        $em->flush();

        $menuAmministrazione = new \Fi\CoreBundle\Entity\menuApplicazione();
        //$menu->setPadre("");
        $menuAmministrazione->setNome('Amministrazione');
        $menuAmministrazione->setAttivo(true);
        $menuAmministrazione->setOrdine(20);
        $em->persist($menuAmministrazione);
        $em->flush();

        $menuop = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menuop->setPadre($menuAmministrazione->getId());
        $menuop->setNome('Operatori');
        $menuop->setPercorso('Operatori');
        $menuop->setAttivo(true);
        $menuop->setOrdine(10);
        $em->persist($menuop);

        $menuruo = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menuruo->setPadre($menuAmministrazione->getId());
        $menuruo->setNome('Ruoli');
        $menuruo->setPercorso('Ruoli');
        $menuruo->setAttivo(true);
        $menuruo->setOrdine(20);
        $em->persist($menuruo);

        $menuapp = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menuapp->setPadre($menuAmministrazione->getId());
        $menuapp->setNome('Permessi');
        $menuapp->setPercorso('Permessi');
        $menuapp->setAttivo(true);
        $menuapp->setOrdine(30);
        $em->persist($menuapp);

        $menutbl = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menutbl->setPadre($menuAmministrazione->getId());
        $menutbl->setNome('Gestione tabelle');
        $menutbl->setPercorso('');
        $menutbl->setAttivo(true);
        $menutbl->setOrdine(40);
        $em->persist($menutbl);
        $em->flush();

        $menutbs = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menutbs->setPadre($menutbl->getId());
        $menutbs->setNome('Tabelle');
        $menutbs->setPercorso('Tabelle');
        $menutbs->setAttivo(true);
        $menutbs->setOrdine(10);
        $em->persist($menutbs);

        $menuopt = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menuopt->setPadre($menutbl->getId());
        $menuopt->setNome('Opzioni tabella');
        $menuopt->setPercorso('OpzioniTabella');
        $menuopt->setAttivo(true);
        $menuopt->setOrdine(20);
        $em->persist($menuopt);

        $menumnuapp = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menumnuapp->setPadre($menuAmministrazione->getId());
        $menumnuapp->setNome('Menu Applicazione');
        $menumnuapp->setPercorso('MenuApplicazione_container');
        $menumnuapp->setAttivo(true);
        $menumnuapp->setOrdine(50);
        $em->persist($menumnuapp);

        $menuutil = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menuutil->setPadre($menuAmministrazione->getId());
        $menuutil->setNome('Utilità');
        $menuutil->setPercorso('fi_pannello_amministrazione_homepage');
        $menuutil->setAttivo(true);
        $menuutil->setOrdine(100);
        $em->persist($menuutil);

        $menudemo = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menudemo->setPadre($menuAmministrazione->getId());
        $menudemo->setNome('FiDemo');
        $menudemo->setPercorso('fi_demo_index');
        $menudemo->setAttivo(false);
        $menudemo->setOrdine(150);
        $em->persist($menudemo);
        $em->flush();
    }

}

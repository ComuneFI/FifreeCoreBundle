<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class Fifree2installCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('fifree2:install')
                ->setDescription('Installazione ambiente fifree')
                ->setHelp('Crea il database, un utente amministratore e i dati di default')
                ->addArgument('admin', InputArgument::REQUIRED, 'Username per amministratore')
                ->addArgument('adminpass', InputArgument::REQUIRED, 'Password per amministratore')
                ->addArgument('adminemail', InputArgument::REQUIRED, 'Email per amministratore')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        $userManipulator = $this->getContainer()->get('fifree.fos_user.util.user_manipulator');

        $adminPassword = $adminpass;
        $adminUsername = $admin;
        $adminEmail = $adminemail;
        $isActive = true;
        $isSuperAdmin = true;
        $userManipulator->create($adminUsername, $adminPassword, $adminEmail, $isActive, $isSuperAdmin);

        $this->loadDefaultValues($admin);
    }

    private function loadDefaultValues($admin)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $ruolos = new \Fi\CoreBundle\Entity\Ruoli();
        $ruolos->setRuolo('Super Admin');
        $ruolos->setPaginainiziale('/adminpanel');
        $ruolos->setIsSuperadmin(true);
        $ruolos->setIsAdmin(true);
        $ruolos->setIsUser(false);
        $em->persist($ruolos);
        $ruoloa = new \Fi\CoreBundle\Entity\Ruoli();
        $ruoloa->setRuolo('Amministratore');
        $ruoloa->setPaginainiziale('/adminpanel');
        $ruoloa->setIsSuperadmin(false);
        $ruoloa->setIsAdmin(true);
        $ruoloa->setIsUser(false);
        $em->persist($ruoloa);
        $ruolou = new \Fi\CoreBundle\Entity\Ruoli();
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

        $this->insertDefaultFfTables($em);

        $permessimnuapp = new \Fi\CoreBundle\Entity\Permessi();
        $permessimnuapp->setRuoli($ruolo);
        $permessimnuapp->setModulo('MenuApplicazione');
        $permessimnuapp->setCrud('crud');
        $em->persist($permessimnuapp);

        $permessiopt = new \Fi\CoreBundle\Entity\Permessi();
        $permessiopt->setRuoli($ruolo);
        $permessiopt->setModulo('OpzioniTabella');
        $permessiopt->setCrud('crud');
        $em->persist($permessiopt);

        $permessitbl = new \Fi\CoreBundle\Entity\Permessi();
        $permessitbl->setRuoli($ruolo);
        $permessitbl->setModulo('Tabelle');
        $permessitbl->setCrud('crud');
        $em->persist($permessitbl);

        $permessi = new \Fi\CoreBundle\Entity\Permessi();
        $permessi->setRuoli($ruolo);
        $permessi->setModulo('Permessi');
        $permessi->setCrud('crud');
        $em->persist($permessi);

        $permessiope = new \Fi\CoreBundle\Entity\Permessi();
        $permessiope->setRuoli($ruolo);
        $permessiope->setModulo('Operatori');
        $permessiope->setCrud('cru');
        $em->persist($permessiope);

        $permessiruo = new \Fi\CoreBundle\Entity\Permessi();
        $permessiruo->setRuoli($ruolo);
        $permessiruo->setModulo('Ruoli');
        $permessiruo->setCrud('crud');
        $em->persist($permessiruo);

        $permessifp = new \Fi\CoreBundle\Entity\Permessi();
        $permessifp->setRuoli($ruolo);
        $permessifp->setModulo('Ffprincipale');
        $permessifp->setCrud('crud');
        $em->persist($permessifp);

        $permessifs = new \Fi\CoreBundle\Entity\Permessi();
        $permessifs->setRuoli($ruolo);
        $permessifs->setModulo('Ffsecondaria');
        $permessifs->setCrud('crud');
        $em->persist($permessifs);

        $tabelle = new \Fi\CoreBundle\Entity\Tabelle();
        $tabelle->setNometabella('*');
        $em->persist($tabelle);

        $tabelleUno = new \Fi\CoreBundle\Entity\Tabelle();
        $tabelleUno->setNometabella('Ffsecondaria');
        $tabelleUno->setNomecampo('ffprincipale');
        $tabelleUno->setMostraindex(true);
        $tabelleUno->setMostrastampa(true);
        $tabelleUno->setRegistrastorico(true);
        $em->persist($tabelleUno);

        $tabelleDue = new \Fi\CoreBundle\Entity\Tabelle();
        $tabelleDue->setNometabella('Ffsecondaria');
        $tabelleDue->setNomecampo('descsec');
        $tabelleDue->setMostraindex(true);
        $tabelleDue->setMostrastampa(true);
        $tabelleDue->setRegistrastorico(true);
        $em->persist($tabelleDue);


        $opzionitabelle = new \Fi\CoreBundle\Entity\OpzioniTabella();
        $opzionitabelle->setTabelle($tabelle);
        $opzionitabelle->setParametro('titolo');
        $opzionitabelle->setValore('Elenco dati per %tabella%');
        $em->persist($opzionitabelle);

        $opzionitabelleag = new \Fi\CoreBundle\Entity\OpzioniTabella();
        $opzionitabelleag->setTabelle($tabelle);
        $opzionitabelleag->setDescrizione('Altezza Griglia');
        $opzionitabelleag->setParametro('altezzagriglia');
        $opzionitabelleag->setValore(400);
        $em->persist($opzionitabelleag);

        $em->flush();
    }

    private function insertDefaultMenu($em)
    {
        $menutabelle = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menutabelle->setNome('Tabelle');
        $menutabelle->setAttivo(true);
        $menutabelle->setOrdine(10);
        $em->persist($menutabelle);
        $em->flush();

        $menufp = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menufp->setPadre($menutabelle->getId());
        $menufp->setNome('FFprincipale');
        $menufp->setPercorso('Ffprincipale');
        $menufp->setAttivo(true);
        $menufp->setOrdine(10);
        $em->persist($menufp);

        $menufs = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menufs->setPadre($menutabelle->getId());
        $menufs->setNome('FFsecondaria');
        $menufs->setPercorso('Ffsecondaria');
        $menufs->setAttivo(true);
        $menufs->setOrdine(10);
        $em->persist($menufs);
        $em->flush();

        $menuAmministrazione = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menuAmministrazione->setNome('Amministrazione');
        $menuAmministrazione->setAttivo(true);
        $menuAmministrazione->setOrdine(20);
        $em->persist($menuAmministrazione);
        $em->flush();

        $menuop = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menuop->setPadre($menuAmministrazione->getId());
        $menuop->setNome('Operatori');
        $menuop->setPercorso('Operatori');
        $menuop->setAttivo(true);
        $menuop->setOrdine(10);
        $em->persist($menuop);

        $menuruo = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menuruo->setPadre($menuAmministrazione->getId());
        $menuruo->setNome('Ruoli');
        $menuruo->setPercorso('Ruoli');
        $menuruo->setAttivo(true);
        $menuruo->setOrdine(20);
        $em->persist($menuruo);

        $menuapp = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menuapp->setPadre($menuAmministrazione->getId());
        $menuapp->setNome('Permessi');
        $menuapp->setPercorso('Permessi');
        $menuapp->setAttivo(true);
        $menuapp->setOrdine(30);
        $em->persist($menuapp);

        $menutbl = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menutbl->setPadre($menuAmministrazione->getId());
        $menutbl->setNome('Gestione tabelle');
        $menutbl->setPercorso('');
        $menutbl->setAttivo(true);
        $menutbl->setOrdine(40);
        $em->persist($menutbl);
        $em->flush();

        $menutbs = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menutbs->setPadre($menutbl->getId());
        $menutbs->setNome('Tabelle');
        $menutbs->setPercorso('Tabelle');
        $menutbs->setAttivo(true);
        $menutbs->setOrdine(10);
        $em->persist($menutbs);

        $menuopt = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menuopt->setPadre($menutbl->getId());
        $menuopt->setNome('Opzioni tabella');
        $menuopt->setPercorso('OpzioniTabella');
        $menuopt->setAttivo(true);
        $menuopt->setOrdine(20);
        $em->persist($menuopt);

        $menumnuapp = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menumnuapp->setPadre($menuAmministrazione->getId());
        $menumnuapp->setNome('Menu Applicazione');
        $menumnuapp->setPercorso('MenuApplicazione_container');
        $menumnuapp->setAttivo(true);
        $menumnuapp->setOrdine(50);
        $em->persist($menumnuapp);

        $menuutil = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menuutil->setPadre($menuAmministrazione->getId());
        $menuutil->setNome('Utilità');
        $menuutil->setPercorso('fi_pannello_amministrazione_homepage');
        $menuutil->setAttivo(true);
        $menuutil->setOrdine(100);
        $em->persist($menuutil);

        $menudemo = new \Fi\CoreBundle\Entity\MenuApplicazione();
        $menudemo->setPadre($menuAmministrazione->getId());
        $menudemo->setNome('FiDemo');
        $menudemo->setPercorso('fi_demo_index');
        $menudemo->setAttivo(false);
        $menudemo->setOrdine(150);
        $em->persist($menudemo);
        $em->flush();
    }

    private function insertDefaultFfTables($em)
    {
        $ffprincipalerow = new \Fi\CoreBundle\Entity\Ffprincipale();
        $ffprincipalerow->setDescrizione('Descrizione primo record');
        $em->persist($ffprincipalerow);
        $em->flush();
        $ffsecondariarow1 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow1->setFfprincipale($ffprincipalerow);
        $ffsecondariarow1->setDescsec('1° secondaria legato al 1° record principale');
        $ffsecondariarow1->setData(new \DateTime());
        $ffsecondariarow1->setIntero(10);
        $ffsecondariarow1->setImporto(12.34);
        $ffsecondariarow1->setNota('Super Nota ffsecondaria');
        $ffsecondariarow1->setAttivo(true);
        $em->persist($ffsecondariarow1);

        $ffsecondariarow2 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow2->setFfprincipale($ffprincipalerow);
        $ffsecondariarow2->setDescsec('2° secondaria legato al 1° record principale');
        $ffsecondariarow2->setData(new \DateTime());
        $ffsecondariarow2->setIntero(1);
        $ffsecondariarow2->setImporto(1.23);
        $ffsecondariarow2->setNota('Nota ffsecondaria');
        $ffsecondariarow2->setAttivo(true);

        $em->persist($ffsecondariarow2);

        $ffsecondariarow3 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow3->setFfprincipale($ffprincipalerow);
        $ffsecondariarow3->setDescsec('3° secondaria legato al 1° record principale');
        $ffsecondariarow3->setData(new \DateTime());
        $ffsecondariarow3->setIntero(10);
        $ffsecondariarow3->setImporto(11.34);
        $ffsecondariarow3->setNota('Nota 3° ffsecondaria');
        $ffsecondariarow3->setAttivo(false);

        $em->persist($ffsecondariarow3);

        $ffsecondariarow4 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow4->setFfprincipale($ffprincipalerow);
        $ffsecondariarow4->setDescsec('4° secondaria legato al 1° record principale');
        $ffsecondariarow4->setData(new \DateTime());
        $ffsecondariarow4->setIntero(101);
        $ffsecondariarow4->setImporto(101.34);
        $ffsecondariarow4->setNota('Nota 4° ffsecondaria');
        $ffsecondariarow4->setAttivo(true);

        $em->persist($ffsecondariarow4);

        $ffsecondariarow5 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow5->setFfprincipale($ffprincipalerow);
        $ffsecondariarow5->setDescsec('5° secondaria legato al 1° record principale');
        $ffsecondariarow5->setData(new \DateTime());
        $ffsecondariarow5->setIntero(101);
        $ffsecondariarow5->setImporto(101.34);
        $ffsecondariarow5->setNota('Nota 4° ffsecondaria');
        $ffsecondariarow5->setAttivo(true);

        $em->persist($ffsecondariarow5);

        $ffprincipale = new \Fi\CoreBundle\Entity\Ffprincipale();
        $ffprincipale->setDescrizione('Descrizione secondo record');
        $em->persist($ffprincipale);

        $ffsecondariarow6 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow6->setFfprincipale($ffprincipale);
        $ffsecondariarow6->setDescsec('6° secondaria legato al 2° record principale');
        $ffsecondariarow6->setData(new \DateTime());
        $ffsecondariarow6->setIntero(10006);
        $ffsecondariarow6->setImporto(10006.12);
        $ffsecondariarow6->setNota('Nota altra ffsecondaria');
        $ffsecondariarow6->setAttivo(true);

        $em->persist($ffsecondariarow6);

        $ffsecondariarow7 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow7->setFfprincipale($ffprincipale);
        $ffsecondariarow7->setDescsec('7° secondaria legato al 2° record principale');
        $ffsecondariarow7->setData(new \DateTime());
        $ffsecondariarow7->setIntero(10007);
        $ffsecondariarow7->setImporto(10007.22);
        $ffsecondariarow7->setNota('Nota altra 7 ffsecondaria');
        $ffsecondariarow7->setAttivo(false);

        $ffsecondariarow8 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow8->setFfprincipale($ffprincipale);
        $ffsecondariarow8->setDescsec('8° secondaria legato al 2° record principale');
        $ffsecondariarow8->setData(new \DateTime());
        $ffsecondariarow8->setIntero(10008);
        $ffsecondariarow8->setImporto(10008.22);
        $ffsecondariarow8->setNota('Nota altra 8 ffsecondaria');
        $ffsecondariarow8->setAttivo(true);

        $em->persist($ffsecondariarow7);
        $ffsecondariarow9 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow9->setFfprincipale($ffprincipale);
        $ffsecondariarow9->setDescsec('9° secondaria legato al 2° "record principale"');
        $ffsecondariarow9->setData(new \DateTime());
        $ffsecondariarow9->setIntero(1000);
        $ffsecondariarow9->setImporto(1000.12);
        $ffsecondariarow9->setNota('Nota altra ffsecondaria');
        $ffsecondariarow9->setAttivo(true);
        $em->persist($ffsecondariarow9);

        $ffsecondariarow10 = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffsecondariarow10->setFfprincipale($ffprincipale);
        $ffsecondariarow10->setDescsec("10° secondaria legato al 2° record principale ed è l'ultimo record");
        $ffsecondariarow10->setData(new \DateTime());
        $ffsecondariarow10->setIntero(1100);
        $ffsecondariarow10->setImporto(1100.99);
        $ffsecondariarow10->setNota('Nota 10° altra ffsecondaria');
        $ffsecondariarow10->setAttivo(false);
        $em->persist($ffsecondariarow10);
    }
}

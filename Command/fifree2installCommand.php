<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
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
            exit;
        }
        if (!$adminpass) {
            echo "Inserire la password per dell'amministratore";
            exit;
        }
        if (!$adminemail) {
            echo "Inserire la mail dell'amministratore";
            exit;
        }

        $commanddb = $this->getApplication()->find('fifree2:createdatabase');
        $arguments = array('command' => 'fifree2:createdatabase');
        $input = new ArrayInput($arguments);
        $returnCode = $commanddb->run($input, $output);
        
        $commandschema = $this->getApplication()->find('doctrine:schema:create');
        $arguments = array('');
        $input = new ArrayInput($arguments);
        $returnCode = $commandschema->run($input, $output);

        $commandfos = $this->getApplication()->find('fos:user:create');
        $arguments = array('command' => 'fos:user:create','--super-admin'=>true,"username"=>$admin,"email"=>$adminemail,"password"=>$adminpass);
        $input = new ArrayInput($arguments);
        $returnCode = $commandfos->run($input, $output);
        
        $this->loadDefaultValues($admin);
    }

    private function loadDefaultValues($admin) {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $ruolo = new \Fi\CoreBundle\Entity\ruoli();
        $ruolo->setRuolo("Super Admin");
        $ruolo->setPaginainiziale("/adminpanel");
        $ruolo->setIsSuperadmin(true);
        $ruolo->setIsAdmin(true);
        $ruolo->setIsUser(false);
        $em->persist($ruolo);
        $ruolo = new \Fi\CoreBundle\Entity\ruoli();
        $ruolo->setRuolo("Amministratore");
        $ruolo->setPaginainiziale("/adminpanel");
        $ruolo->setIsSuperadmin(false);
        $ruolo->setIsAdmin(true);
        $ruolo->setIsUser(false);
        $em->persist($ruolo);
        $ruolo = new \Fi\CoreBundle\Entity\ruoli();
        $ruolo->setRuolo("Utente");
        $ruolo->setPaginainiziale("/ffprincipale");
        $ruolo->setIsSuperadmin(false);
        $ruolo->setIsAdmin(false);
        $ruolo->setIsUser(true);
        $em->persist($ruolo);
        $em->flush();

        //Si tiene in memoria l'id del super admin
        $ruolo = $em->getRepository('FiCoreBundle:ruoli')->findOneBy(array('is_superadmin' => true)); //SuperAdmin
        $operatore = $em->getRepository('FiCoreBundle:operatori')->findOneByUsername($admin);
        $operatore->setRuoli($ruolo);
        $em->persist($operatore);
        $em->flush();

        $menutabelle = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menutabelle->setNome("Tabelle");
        $menutabelle->setAttivo(true);
        $menutabelle->setOrdine(10);
        $em->persist($menutabelle);
        $em->flush();

        $menu = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menu->setPadre($menutabelle->getId());
        $menu->setNome("FFprincipale");
        $menu->setPercorso("ffprincipale");
        $menu->setAttivo(true);
        $menu->setOrdine(10);
        $em->persist($menu);

        $menu = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menu->setPadre($menutabelle->getId());
        $menu->setNome("FFsecondaria");
        $menu->setPercorso("ffsecondaria");
        $menu->setAttivo(true);
        $menu->setOrdine(10);
        $em->persist($menu);
        $em->flush();

        $menuAmministrazione = new \Fi\CoreBundle\Entity\menuApplicazione();
        //$menu->setPadre("");
        $menuAmministrazione->setNome("Amministrazione");
        $menuAmministrazione->setAttivo(true);
        $menuAmministrazione->setOrdine(20);
        $em->persist($menuAmministrazione);
        $em->flush();

        $menu = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menu->setPadre($menuAmministrazione->getId());
        $menu->setNome("Operatori");
        $menu->setPercorso("operatori");
        $menu->setAttivo(true);
        $menu->setOrdine(10);
        $em->persist($menu);

        $menu = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menu->setPadre($menuAmministrazione->getId());
        $menu->setNome("Ruoli");
        $menu->setPercorso("ruoli");
        $menu->setAttivo(true);
        $menu->setOrdine(20);
        $em->persist($menu);

        $menu = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menu->setPadre($menuAmministrazione->getId());
        $menu->setNome("Permessi");
        $menu->setPercorso("permessi");
        $menu->setAttivo(true);
        $menu->setOrdine(30);
        $em->persist($menu);

        $menutbl = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menutbl->setPadre($menuAmministrazione->getId());
        $menutbl->setNome("Gestione tabelle");
        $menutbl->setPercorso("");
        $menutbl->setAttivo(true);
        $menutbl->setOrdine(40);
        $em->persist($menutbl);
        $em->flush();

        $menu = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menu->setPadre($menutbl->getId());
        $menu->setNome("Tabelle");
        $menu->setPercorso("tabelle");
        $menu->setAttivo(true);
        $menu->setOrdine(10);
        $em->persist($menu);

        $menu = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menu->setPadre($menutbl->getId());
        $menu->setNome("Opzioni tabella");
        $menu->setPercorso("opzioniTabella");
        $menu->setAttivo(true);
        $menu->setOrdine(20);
        $em->persist($menu);

        $menu = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menu->setPadre($menuAmministrazione->getId());
        $menu->setNome("Menu Applicazione");
        $menu->setPercorso("menuApplicazione_container");
        $menu->setAttivo(true);
        $menu->setOrdine(50);
        $em->persist($menu);

        $menu = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menu->setPadre($menuAmministrazione->getId());
        $menu->setNome("Utilità");
        $menu->setPercorso("fi_pannello_amministrazione_homepage");
        $menu->setAttivo(true);
        $menu->setOrdine(100);
        $em->persist($menu);

        $menu = new \Fi\CoreBundle\Entity\menuApplicazione();
        $menu->setPadre($menuAmministrazione->getId());
        $menu->setNome("FiDemo");
        $menu->setPercorso("fi_demo_index");
        $menu->setAttivo(false);
        $menu->setOrdine(150);
        $em->persist($menu);
        $em->flush();


        $permessi = new \Fi\CoreBundle\Entity\permessi();
        $permessi->setRuoli($ruolo);
        $permessi->setModulo("menuApplicazione");
        $permessi->setCrud("crud");
        $em->persist($permessi);

        $permessi = new \Fi\CoreBundle\Entity\permessi();
        $permessi->setRuoli($ruolo);
        $permessi->setModulo("opzioniTabella");
        $permessi->setCrud("crud");
        $em->persist($permessi);

        $permessi = new \Fi\CoreBundle\Entity\permessi();
        $permessi->setRuoli($ruolo);
        $permessi->setModulo("tabelle");
        $permessi->setCrud("crud");
        $em->persist($permessi);

        $permessi = new \Fi\CoreBundle\Entity\permessi();
        $permessi->setRuoli($ruolo);
        $permessi->setModulo("permessi");
        $permessi->setCrud("crud");
        $em->persist($permessi);

        $permessi = new \Fi\CoreBundle\Entity\permessi();
        $permessi->setRuoli($ruolo);
        $permessi->setModulo("operatori");
        $permessi->setCrud("cru");
        $em->persist($permessi);

        $permessi = new \Fi\CoreBundle\Entity\permessi();
        $permessi->setRuoli($ruolo);
        $permessi->setModulo("ruoli");
        $permessi->setCrud("crud");
        $em->persist($permessi);

        $permessi = new \Fi\CoreBundle\Entity\permessi();
        $permessi->setRuoli($ruolo);
        $permessi->setModulo("ffprincipale");
        $permessi->setCrud("crud");
        $em->persist($permessi);

        $permessi = new \Fi\CoreBundle\Entity\permessi();
        $permessi->setRuoli($ruolo);
        $permessi->setModulo("ffsecondaria");
        $permessi->setCrud("crud");
        $em->persist($permessi);


        $ffprincipale = new \Fi\CoreBundle\Entity\ffprincipale();
        $ffprincipale->setDescrizione("Descrizione primo record");
        $em->persist($ffprincipale);
        $em->flush();
        $ffsecondaria = new \Fi\CoreBundle\Entity\ffsecondaria();
        $ffsecondaria->setFfprincipale($ffprincipale);
        $ffsecondaria->setDescsec("1° secondaria legato al 1° record principale");
        $em->persist($ffsecondaria);

        $ffsecondaria = new \Fi\CoreBundle\Entity\ffsecondaria();
        $ffsecondaria->setFfprincipale($ffprincipale);
        $ffsecondaria->setDescsec("2° secondaria legato al 1° record principale");
        $em->persist($ffsecondaria);


        $ffprincipale = new \Fi\CoreBundle\Entity\ffprincipale();
        $ffprincipale->setDescrizione("Descrizione secondo record");
        $em->persist($ffprincipale);

        $ffsecondaria = new \Fi\CoreBundle\Entity\ffsecondaria();
        $ffsecondaria->setFfprincipale($ffprincipale);
        $ffsecondaria->setDescsec("3° secondaria legato al 2° record principale");
        $em->persist($ffsecondaria);


        $tabelle = new \Fi\CoreBundle\Entity\tabelle();
        $tabelle->setNometabella("*");
        $em->persist($tabelle);

        $opzionitabelle = new \Fi\CoreBundle\Entity\opzioniTabella;
        $opzionitabelle->setTabelle($tabelle);
        $opzionitabelle->setParametro('titolo');
        $opzionitabelle->setValore('Elenco dati per %tabella%');
        $em->persist($opzionitabelle);

        $em->flush();
    }

}

?>
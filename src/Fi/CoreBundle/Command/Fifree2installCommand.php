<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class Fifree2installCommand extends ContainerAwareCommand
{

    protected $fixtureFile;

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
        $this->fixtureFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fixtures.yml';

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

        $this->generateDefaultData($admin, $adminemail);

        $commanddata = $this->getApplication()->find('fifree2:configuratorimport');
        $argumentsdata = array(
            'command' => 'fifree2:configuratorimport',
            array("--truncatetables" => true)
        );
        $inputd = new ArrayInput($argumentsdata);
        $commanddata->run($inputd, $output);

        $fs = new Filesystem();
        $fs->remove($this->fixtureFile);

        $userManipulator = $this->getContainer()->get('fifree.fos_user.util.user_manipulator');

        $userManipulator->changePassword($admin, $adminpass);
    }

    /**
     * This will suppress UnusedLocalVariable
     * warnings in this method
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function generateDefaultData($admin, $adminemail)
    {
        $defaultData = <<<EOF
Fi\CoreBundle\Entity\Ruoli:
  -
    id: 1
    ruolo: 'Super Admin'
    paginainiziale: /adminpanel
    is_superadmin: true
    is_admin: true
    is_user: false
  -
    id: 2
    ruolo: Amministratore
    paginainiziale: /adminpanel
    is_superadmin: false
    is_admin: true
    is_user: false
  -
    id: 3
    ruolo: Utente
    paginainiziale: /ffprincipale
    is_superadmin: false
    is_admin: false
    is_user: true
Fi\CoreBundle\Entity\Operatori:
  -
    username: $admin
    usernameCanonical: $admin
    email: $adminemail
    emailCanonical: $adminemail
    enabled: true
    salt: null
    password: $admin
    lastLogin: null
    confirmationToken: null
    passwordRequestedAt: null
    roles:
      - ROLE_SUPER_ADMIN
    id: 1
    operatore: $admin
    ruoli_id: 1
Fi\CoreBundle\Entity\Permessi:
  -
    id: 1
    modulo: MenuApplicazione
    crud: crud
    operatori_id: null
    ruoli_id: 1
  -
    id: 2
    modulo: OpzioniTabella
    crud: crud
    operatori_id: null
    ruoli_id: 1
  -
    id: 3
    modulo: Tabelle
    crud: crud
    operatori_id: null
    ruoli_id: 1
  -
    id: 4
    modulo: Permessi
    crud: crud
    operatori_id: null
    ruoli_id: 1
  -
    id: 5
    modulo: Operatori
    crud: cru
    operatori_id: null
    ruoli_id: 1
  -
    id: 6
    modulo: Ruoli
    crud: crud
    operatori_id: null
    ruoli_id: 1
  -
    id: 7
    modulo: Ffprincipale
    crud: crud
    operatori_id: null
    ruoli_id: 1
  -
    id: 8
    modulo: Ffsecondaria
    crud: crud
    operatori_id: null
    ruoli_id: 1
Fi\CoreBundle\Entity\Tabelle:
  -
    id: 1
    nometabella: '*'
    nomecampo: null
    mostraindex: null
    ordineindex: null
    larghezzaindex: null
    etichettaindex: null
    mostrastampa: null
    ordinestampa: null
    larghezzastampa: null
    etichettastampa: null
    operatori_id: null
    registrastorico: null
  -
    id: 2
    nometabella: Ffsecondaria
    nomecampo: ffprincipale
    mostraindex: true
    ordineindex: null
    larghezzaindex: null
    etichettaindex: null
    mostrastampa: true
    ordinestampa: null
    larghezzastampa: null
    etichettastampa: null
    operatori_id: null
    registrastorico: true
  -
    id: 3
    nometabella: Ffsecondaria
    nomecampo: descsec
    mostraindex: true
    ordineindex: null
    larghezzaindex: null
    etichettaindex: null
    mostrastampa: true
    ordinestampa: null
    larghezzastampa: null
    etichettastampa: null
    operatori_id: null
    registrastorico: true
Fi\CoreBundle\Entity\OpzioniTabella:
  -
    id: 1
    tabelle_id: 1
    descrizione: null
    parametro: titolo
    valore: 'Elenco dati per %tabella%'
  -
    id: 2
    tabelle_id: 1
    descrizione: 'Altezza Griglia'
    parametro: altezzagriglia
    valore: '400'
  -
    id: 3
    tabelle_id: 1
    descrizione: 'Esegue filtri con invio in testata griglia'
    parametro: filterToolbar_searchOnEnter
    valore: true
  -
    id: 4
    tabelle_id: 1
    descrizione: 'Aggiunge filtri di default in testata griglia'
    parametro: filterToolbar_searchOperators
    valore: true
Fi\CoreBundle\Entity\Ffprincipale:
  -
    id: 1
    descrizione: 'Descrizione primo record'
  -
    id: 2
    descrizione: 'Descrizione secondo record'
Fi\CoreBundle\Entity\Ffsecondaria:
  -
    id: 1
    descsec: '1° secondaria legato al 1° record principale'
    ffprincipale_id: 1
    data: 2018-02-15T00:00:00+01:00
    intero: 10
    importo: 12.34
    nota: 'Super Nota ffsecondaria'
    attivo: true
  -
    id: 2
    descsec: '2° secondaria legato al 1° record principale'
    ffprincipale_id: 1
    data: 2018-02-15T00:00:00+01:00
    intero: 1
    importo: 1.23
    nota: 'Nota ffsecondaria'
    attivo: true
  -
    id: 3
    descsec: '3° secondaria legato al 1° record principale'
    ffprincipale_id: 1
    data: 2018-02-15T00:00:00+01:00
    intero: 10
    importo: 11.34
    nota: 'Nota 3° ffsecondaria'
    attivo: false
  -
    id: 4
    descsec: '4° secondaria legato al 1° record principale'
    ffprincipale_id: 1
    data: 2018-02-15T00:00:00+01:00
    intero: 101
    importo: 101.34
    nota: 'Nota 4° ffsecondaria'
    attivo: true
  -
    id: 5
    descsec: '5° secondaria legato al 1° record principale'
    ffprincipale_id: 1
    data: 2018-02-15T00:00:00+01:00
    intero: 101
    importo: 101.34
    nota: 'Nota 4° ffsecondaria'
    attivo: true
  -
    id: 6
    descsec: '6° secondaria legato al 2° record principale'
    ffprincipale_id: 2
    data: 2018-02-15T00:00:00+01:00
    intero: 10006
    importo: 10006.12
    nota: 'Nota altra ffsecondaria'
    attivo: true
  -
    id: 7
    descsec: '7° secondaria legato al 2° record principale'
    ffprincipale_id: 2
    data: 2018-02-15T00:00:00+01:00
    intero: 10007
    importo: 10007.22
    nota: 'Nota altra 7 ffsecondaria'
    attivo: false
  -
    id: 8
    descsec: '9° secondaria legato al 2° "record principale"'
    ffprincipale_id: 2
    data: 2018-02-15T00:00:00+01:00
    intero: 1000
    importo: 1000.12
    nota: 'Nota altra ffsecondaria'
    attivo: true
  -
    id: 9
    descsec: '10° secondaria legato al 2° record principale ed è l''ultimo record'
    ffprincipale_id: 2
    data: 2018-02-15T00:00:00+01:00
    intero: 1100
    importo: 1100.99
    nota: 'Nota 10° altra ffsecondaria'
    attivo: false
Fi\CoreBundle\Entity\MenuApplicazione:
  -
    id: 1
    nome: Tabelle
    percorso: null
    padre: null
    ordine: 10
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 2
    nome: FFprincipale
    percorso: Ffprincipale
    padre: 1
    ordine: 10
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 3
    nome: FFsecondaria
    percorso: Ffsecondaria
    padre: 1
    ordine: 10
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 4
    nome: Amministrazione
    percorso: null
    padre: null
    ordine: 20
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 5
    nome: Operatori
    percorso: Operatori
    padre: 4
    ordine: 10
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 6
    nome: Ruoli
    percorso: Ruoli
    padre: 4
    ordine: 20
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 7
    nome: Permessi
    percorso: Permessi
    padre: 4
    ordine: 30
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 8
    nome: 'Gestione tabelle'
    percorso: null
    padre: 4
    ordine: 40
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 9
    nome: Tabelle
    percorso: Tabelle
    padre: 8
    ordine: 10
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 10
    nome: 'Opzioni tabella'
    percorso: OpzioniTabella
    padre: 8
    ordine: 20
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 11
    nome: 'Menu Applicazione'
    percorso: MenuApplicazione_container
    padre: 4
    ordine: 50
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 12
    nome: Utilità
    percorso: fi_pannello_amministrazione_homepage
    padre: 4
    ordine: 100
    attivo: true
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
  -
    id: 13
    nome: FiDemo
    percorso: fi_demo_index
    padre: 4
    ordine: 150
    attivo: false
    target: null
    tag: null
    notifiche: null
    autorizzazionerichiesta: null
    percorsonotifiche: null
EOF;
        $fs = new Filesystem();
        $fs->dumpFile($this->fixtureFile, $defaultData);
    }
}

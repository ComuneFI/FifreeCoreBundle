<?php

namespace Fi\CoreBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

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
        $todaydt = new DateTime();
        $today = $todaydt->format("Y-m-d") . "T00:00:00+01:00";
        
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
    -
        id: 4
        ruolo: Ffprincipale
        paginainiziale: /ffprincipale
        is_superadmin: false
        is_admin: false
        is_user: true
    -
        id: 5
        ruolo: Ffsecondaria
        paginainiziale: /ffsecondaria
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
    -
        username: ffprincipalec
        usernameCanonical: ffprincipalec
        email: ffprincipalec@ffprincipale.it
        emailCanonical: ffprincipalec@ffprincipale.it
        enabled: true
        salt: TsslJJBJVZRzYSN1XrR97bTj33MEM0hf5b0xaFgbtF8
        password: q3EgysATetLjBY1EEcLJX54wwWv6z/Vv9ftTj+wbEg8E+YfQTHHoAoII0ig40fThp23BdyZ88iOelJ65EYmpwA==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 2
        operatore: null
        ruoli_id: null
    -
        username: ffprincipaler
        usernameCanonical: ffprincipaler
        email: ffprincipaler@ffprincipale.it
        emailCanonical: ffprincipaler@ffprincipale.it
        enabled: true
        salt: FYjiqs9fg2Rye/rQW2kZOVugzSRYvDEvOeXaRL1NH.Y
        password: KZaxi0Vsje3TWwa5/5P/oHy56N0ZPj/IqdeAd8DPuMPFgjI89HcoWrF1PQCEvdCL0p3xuIA+R9zoxC6aw/q1NA==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 3
        operatore: null
        ruoli_id: null
    -
        username: ffprincipaleu
        usernameCanonical: ffprincipaleu
        email: ffprincipaleu@ffprincipale.it
        emailCanonical: ffprincipaleu@ffprincipale.it
        enabled: true
        salt: P4NejU4s9XNhkisXm/TX1rFO3Yx6DbO28H8c.kuphq0
        password: M6Q7MArq/XbgCRueH93DaMduzTqhTQxqNxD5syKyXsj9tjerQa2ZzvX8Wgg9JE0pqnv1vJVVFFUmfjguSL0NQg==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 4
        operatore: null
        ruoli_id: null
    -
        username: ffprincipaled
        usernameCanonical: ffprincipaled
        email: ffprincipaled@ffprincipale.it
        emailCanonical: ffprincipaled@ffprincipale.it
        enabled: true
        salt: pE01F9zH7sYq3PqvT4dwykSA.3ax0E7ERZSPxse6/Mw
        password: Pj3I9hXVrvx57CKoUyBxzvjLbuZCAthfXsN6qoz+8FhTN/a0iwkfLikd2DPAy2ePCb3Dv2ukRIxoJSRL/fiAnA==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 5
        operatore: null
        ruoli_id: null
    -
        username: ffsecondariac
        usernameCanonical: ffsecondariac
        email: ffsecondariac@ffsecondaria.it
        emailCanonical: ffsecondariac@ffsecondaria.it
        enabled: true
        salt: rCmWpPixVyq0.ytBt/Uk7juA.KS6JAscAn3ZhNMscM8
        password: sPtyn93cPWF60mKTI5FAoKppJWlX8KGiJKs7DbyxNtA8MKiTl9C4HG9UsNsNdgMexg5/EF5eLFLa4wUqOha37g==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 6
        operatore: null
        ruoli_id: null
    -
        username: ffsecondariar
        usernameCanonical: ffsecondariar
        email: ffsecondariar@ffsecondaria.it
        emailCanonical: ffsecondariar@ffsecondaria.it
        enabled: true
        salt: nFDM1Za4bdC4UQqM8trUWT8T4lVi3N1k4F2.651zWTM
        password: BJ2+8wWV3MKaDN6t3bHQiAn1C3BNqiIDMCstMPinO1qOwBKNcumBdbMdvRC86Az/c/5srb8wpwCHdZZ7bdTbwQ==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 7
        operatore: null
        ruoli_id: null
    -
        username: ffsecondariau
        usernameCanonical: ffsecondariau
        email: ffsecondariau@ffsecondaria.it
        emailCanonical: ffsecondariau@ffsecondaria.it
        enabled: true
        salt: t24V9m5oaJVEHy6bgYmK8R.dZ.WrCrUoj6dR7XUY8H4
        password: SeFRn5cJiRZgWU5Hj623v+4FGF5PI0J3cuNb62EhIrazudvMQCGrvdcZutKNZdhdCM7In8fiTE8DjPUJqg64DQ==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 8
        operatore: null
        ruoli_id: null
    -
        username: ffsecondariad
        usernameCanonical: ffsecondariad
        email: ffsecondariad@ffsecondaria.it
        emailCanonical: ffsecondariad@ffsecondaria.it
        enabled: true
        salt: 6r2f.2sU8C3FMWvAevOnXA1haSXFywcb2DZhICZE9dI
        password: Csop95dNeEv8B6PzVMlzFyZfkLNyU8KaljQlVrWDUtbmNbUPWEFyaFRVOCKVVdlQK+yi+TNDI7sgzrmuwhtg4w==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 9
        operatore: null
        ruoli_id: null
    -
        username: ffprincipale
        usernameCanonical: ffprincipale
        email: ffprincipale@ffprincipale.it
        emailCanonical: ffprincipale@ffprincipale.it
        enabled: true
        salt: .5AIBmH.CKC2oSXdZOFO/ccQew3MP3LVqpFGpvRmrQk
        password: qhutSK2csuIn0UluYXOT3DKLxwMMuddfuEjGaa6iMDnVLjntSPccJTmmCa1wcikzQ8mAAwrhOUAcnmatYhg33g==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 10
        operatore: null
        ruoli_id: 4
    -
        username: ffsecondaria
        usernameCanonical: ffsecondaria
        email: ffsecondaria@ffsecondaria.it
        emailCanonical: ffsecondaria@ffsecondaria.it
        enabled: true
        salt: Ol1WIAFWsAw7DewtYdyFM86T7fWYOYslvm.7C7rwV5c
        password: s/jZyrl4SCACNKiYVq9ie3l4b2Q/AvAhy7eiHPrLxcfeAa/Mn2MvlFkEppQC/JHINIICKjXhNhdZk0PO5vS7IA==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 11
        operatore: null
        ruoli_id: 5
    -
        username: usernoroles
        usernameCanonical: usernoroles
        email: usernoroles@usernoroles.it
        emailCanonical: usernoroles@usernoroles.it
        enabled: true
        salt: nczIukArDyAEH6vvjehM973qvfDjE.WGzkP24umtpfE
        password: Ce0FJ16dd5HfwJ8CbzocZB3UDZWzwvD9l/A3kyJJR1oHoisxGjF06qR4sSj/Nsk8J6aCI1GtgmHbJfeF7TS93w==
        lastLogin: null
        confirmationToken: null
        passwordRequestedAt: null
        roles: {  }
        id: 12
        operatore: null
        ruoli_id: null
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
    -
        id: 9
        modulo: Ffsecondaria
        crud: crud
        operatori_id: null
        ruoli_id: 5
    -
        id: 10
        modulo: Ffprincipale
        crud: crud
        operatori_id: null
        ruoli_id: 4
    -
        id: 11
        modulo: Ffprincipale
        crud: c
        operatori_id: 2
        ruoli_id: null
    -
        id: 12
        modulo: Ffprincipale
        crud: r
        operatori_id: 3
        ruoli_id: null
    -
        id: 13
        modulo: Ffprincipale
        crud: u
        operatori_id: 4
        ruoli_id: null
    -
        id: 14
        modulo: Ffprincipale
        crud: d
        operatori_id: 5
        ruoli_id: null
    -
        id: 15
        modulo: Ffsecondaria
        crud: c
        operatori_id: 6
        ruoli_id: null
    -
        id: 16
        modulo: Ffsecondaria
        crud: r
        operatori_id: 7
        ruoli_id: null
    -
        id: 17
        modulo: Ffsecondaria
        crud: u
        operatori_id: 8
        ruoli_id: null
    -
        id: 18
        modulo: Ffsecondaria
        crud: d
        operatori_id: 9
        ruoli_id: null
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
  -
    id: 4
    nometabella: Ffprincipale
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
  -
    id: 5
    tabelle_id: 4
    descrizione: 'Allarga griglia Ffprincipale'
    parametro: larghezzagriglia
    valore: 800
  -
    id: 6
    tabelle_id: 4
    descrizione: 'Multisearch Ffprincipale'
    parametro: multisearch
    valore: true
  -
    id: 7
    tabelle_id: 4
    descrizione: 'Showconfig Ffprincipale'
    parametro: showconfig
    valore: true
  -
    id: 8
    tabelle_id: 4
    descrizione: 'Show excel Ffprincipale'
    parametro: showexcel
    valore: true
  -
    id: 9
    tabelle_id: 4
    descrizione: 'Overlayopen Ffprincipale'
    parametro: overlayopen
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
    descsec: '1° secondaria legato al 1° record PRINCIPALE'
    ffprincipale_id: 1
    data: $today
    intero: 10
    importo: 12.34
    nota: 'Super Nota ffsecondaria'
    attivo: true
  -
    id: 2
    descsec: '2° SECONDARIA legato al 1° record principale'
    ffprincipale_id: 1
    data: $today
    intero: 1
    importo: 1.23
    nota: 'Nota ffsecondaria'
    attivo: true
  -
    id: 3
    descsec: '3° secondaria legato al 1° record principale'
    ffprincipale_id: 1
    data: $today
    intero: 10
    importo: 11.34
    nota: 'Nota 3° ffsecondaria'
    attivo: false
  -
    id: 4
    descsec: '4° Secondaria legato al 1° record Principale'
    ffprincipale_id: 1
    data: $today
    intero: 101
    importo: 101.34
    nota: 'Nota 4° ffsecondaria'
    attivo: true
  -
    id: 5
    descsec: '5° secondaria legato al 1° record principale'
    ffprincipale_id: 1
    data: $today
    intero: 101
    importo: 101.34
    nota: 'Nota 4° ffsecondaria'
    attivo: true
  -
    id: 6
    descsec: '6° secondaria legato al 2° record principale'
    ffprincipale_id: 2
    data: $today
    intero: 10006
    importo: 10006.12
    nota: 'Nota altra ffsecondaria'
    attivo: true
  -
    id: 7
    descsec: '7° secondaria legato al 2° record principale'
    ffprincipale_id: 2
    data: $today
    intero: 10007
    importo: 10007.22
    nota: 'Nota altra 7 ffsecondaria'
    attivo: false
  -
    id: 8
    descsec: null
    ffprincipale_id: 2
    data: $today
    intero: 1111
    importo: 1111.12
    nota: 'Nota ffsecondaria con descsec null'
    attivo: false
  -
    id: 9
    descsec: '9° secondaria legato al 2° "record principale"'
    ffprincipale_id: 2
    data: $today
    intero: 1000
    importo: 1000.12
    nota: 'Nota altra ffsecondaria'
    attivo: true
  -
    id: 10
    descsec: '10° secondaria legato al 2° record principale ed è l''ultimo record'
    ffprincipale_id: 2
    data: $today
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

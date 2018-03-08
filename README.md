FifreeCoreBundle
=============
[![Build Status](https://travis-ci.org/ComuneFI/FifreeCoreBundle.svg?branch=master)](https://travis-ci.org/ComuneFI/FifreeCoreBundle)
[![Coverage Status](https://coveralls.io/repos/github/ComuneFI/FifreeCoreBundle/badge.svg?branch=master)](https://coveralls.io/github/ComuneFI/FifreeCoreBundle?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ComuneFI/FifreeCoreBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ComuneFI/FifreeCoreBundle/?branch=master)

Intro:
-------------
FiFreeCoreBundle è un bundle per symfony (3.4 o superiori) composto da un insieme di framework Open Source (JQuery, JQuery-ui, JQGrid per il backend e Bootstrap per il frontend), costruito in modo da essere un accelleratore di produttività riusabile e ridistribuibile.
I prodotti creati con FiFree sono facilmente manutenibili, la documentazione dei componenti è ampiamente diffusa on line.
Le funzioni che servono ripetitivamente (p.e. login, creazione di interfacce per le tabelle, etc.) sono nativamente disponibili in tutti i programmi creati con questo prodotto.
Utilizzando doctrine si può generare velocemente una base dati su Mysql, Postgresql o sqlite (viene utilizzato per i test)
FifreeCoreBundle è in grado di convertire uno schema database creato tramite Mysqlworkbench in entity class gestite da symfony tramite doctrine (indipendentemete dal tipo di database scelto).
FifreeCoreBundle è inoltre dotato di un proprio pannello di amministrazione che permette velocemente di pubblicare aggiornamenti (tramite Git/Svn), di creare nuovi form per la procedura che si intende sviluppare, aggiornare lo schema database partendo dal file generato tramite Mysqlworkbench, pulizia della cache, e lancio di comandi shell (con le limitazione dell'utente con cui è in esecuzione il servizio web) tutto tramite pochi click.

Obiettivi, destinatari e contesto: 
-------------
I software sviluppati internamente al Comune di Firenze sono fruiti da due tipi di soggetti: da una parte i colleghi del Comune di Firenze hanno bisogno di accedere a una interfaccia che sia coerente, di semplice utilizzo e pratica. 
Dall’altra parte i cittadini hanno la necessità di accedere ai servizi che il Comune mette a disposizione in modo semplice e intuitivo. 
Per esempio, il software di gestione del Patrimonio Immobiliare è composto da molti moduli, sia rivolti a chi si occupa di gestire il patrimonio internamente, sia ai colleghi che si occupano di gestire i Bandi, sia ai cittadini che possono immettere la domanda direttamente attraverso una semplice interfaccia fruibile anche da tablet e smartphone. 

Installazione:
-------------

- Aggiungere tramite composer:
```
composer require fi/fifreecorebundle
```
- Aggiungere nel file app/AppKernel.php nella funzione registerBundles;
```
    new Fi\CoreBundle\FiCoreBundle(),
```

- Test

```
    rm -rf vendor/ composer.lock
    #Scarico dipendenze
    composer install

    #Preparare il db
    rm tests/var/cache/dbtest.sqlite
    rm -rf test/var/cache/prod
    rm -rf test/var/cache/dev
    rm -rf test/var/cache/test
    bin/console cache:clear --no-warmup
    bin/console fifree:dropdatabase --force
    bin/console fifree:install admin admin admin@admin.it
    chmod 666 tests/var/cache/dbtest.sqlite

    #Assets install
    bin/console assets:install --symlink --relative tests/public
                        

    ##Start server 
    #bin/console server:stop --env=test > /dev/null 2>&1 &
    #bin/console server:start --docroot=tests/public 2>&1 &
    #sh vendor/bin/selenium-server-standalone > /dev/null 2>&1 &

    #Lanciare i test
    ant

    vendor/bin/simple-phpunit

    #stop server
    php bin/console server:stop > /dev/null 2>&1 &
    sudo kill `ps -ef | grep selenium | awk '{ print $2 }'`
   
```

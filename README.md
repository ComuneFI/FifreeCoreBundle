FifreeCoreBundle
=============
[![Build Status](https://travis-ci.org/ComuneFI/FifreeCoreBundle.svg?branch=master)](https://travis-ci.org/ComuneFI/FifreeCoreBundle)
[![Coverage Status](https://coveralls.io/repos/github/ComuneFI/FifreeCoreBundle/badge.svg?branch=master)](https://coveralls.io/github/ComuneFI/FifreeCoreBundle?branch=master)


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
    rm tests/app/dbtest.sqlite
    php bin/console fifree:install admin admin admin@admin.it --env=test
    rm -rf var/cache/prod
    rm -rf var/cache/dev
    rm -rf var/cache/test
    php bin/console cache:clear --env=test

    #Assets install
    php bin/console assets:install --env=test

    #Start server
    php bin/console server:stop --env=test > /dev/null 2>&1 &
    php bin/console server:start --env=test 2>&1 &
    sh vendor/bin/selenium-server-standalone > /dev/null 2>&1 &

    #Lanciare i test
    ant

    vendor/bin/simple-phpunit

    #stop server
    kill `ps -ef | grep selenium | awk '{ print $2 }'`
    php bin/console server:stop --env=test > /dev/null 2>&1 &

```

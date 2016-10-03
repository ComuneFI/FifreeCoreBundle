FifreeCoreBundle
=============
[![Build Status](https://travis-ci.org/ComuneFI/FifreeCoreBundle.svg?branch=master)]
(https://travis-ci.org/ComuneFI/FifreeCoreBundle) [![Coverage Status](https://img.shields.io/coveralls/ComuneFI/FifreeCoreBundle.svg)] 
(https://coveralls.io/r/ComuneFI/FifreeCoreBundle)

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
Scarico dipendenze
```
    composer install
```
Preparare il db
```
    rm app/dbtest.sqlite
    php app/console fifree:install admin admin admin@admin.it --env=test
    rm -rf app/cache/dev
    rm -rf app/cache/test
```
Assets install
```
php app/console assets:install --env=test
```
Start server
```
php app/console server:run --env=test 2>&1 &
sh vendor/bin/selenium-server-standalone > /dev/null 2>&1 &

```
Lanciare i test
```
    vendor/bin/phpunit
```

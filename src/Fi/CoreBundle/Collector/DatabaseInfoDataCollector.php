<?php

namespace Fi\CoreBundle\Collector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\Container;

class DatabaseInfoDataCollector extends DataCollector
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'database_driver' => $this->container->get('database_connection')->getDriver()->getName(),
            'database_host' => $this->container->get('database_connection')->getHost(),
            'database_port' => $this->container->get('database_connection')->getPort(),
            'database_name' => $this->container->get('database_connection')->getDatabase(),
            'database_user' => $this->container->get('database_connection')->getUsername(),
            'database_password' => $this->container->get('database_connection')->getPassword(),
        );
    }

    public function getDatabaseDriver()
    {
        switch ($this->data['database_driver']) {
        case 'pdo_mysql':
            $driverName = 'MySql';
            break;
        case 'pdo_pgsql':
            $driverName = 'PostgreSQL';
            break;
        case 'pdo_sqlite':
            $driverName = 'SQLite';
            break;
        case 'oci8':
            $driverName = 'Oracle';
            break;
        default:
            $driverName = 'Driver non gestito da questo pannello';
            break;
        }

        return $driverName;
    }

    public function getDatabaseHost()
    {
        return $this->data['database_host'];
    }

    public function getDatabasePort()
    {
        return $this->data['database_port'];
    }

    public function getDatabaseName()
    {
        return $this->data['database_name'];
    }

    public function getDatabaseUser()
    {
        return $this->data['database_user'];
    }

    public function getDatabasePassword()
    {
        return $this->data['database_password'];
    }

    public function getName()
    {
        return 'databaseInfo';
    }
}

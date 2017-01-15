<?php

namespace Fi\CoreBundle\Tests\Collector;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DatabaseCollectorTest extends WebTestCase
{

    public function testAction()
    {
        $client = static::createClient();

        // Enable the profiler for the next request
        // (it does nothing if the profiler is not available)
        $client->enableProfiler();

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $client->getKernel()->getContainer()->get("doctrine")->getEntityManager();
        $dbhostconnection = $em->getConnection()->getHost();
        $dbportconnection = $em->getConnection()->getPort();
        $dbdatabaseconnection = $em->getConnection()->getDatabase();
        $dbpwdconnection = $em->getConnection()->getPassword();
        $dbuserconnection = $em->getConnection()->getUsername();

        $crawler = $client->request('GET', '/');

        if ($profile = $client->getProfile()) {
            $this->assertEquals($dbhostconnection, $profile->getCollector('databaseInfo')->getDatabaseHost());
            $this->assertEquals($dbportconnection, $profile->getCollector('databaseInfo')->getDatabasePort());
            $this->assertEquals($dbdatabaseconnection, $profile->getCollector('databaseInfo')->getDatabaseName());
            $this->assertEquals($dbpwdconnection, $profile->getCollector('databaseInfo')->getDatabasePassword());
            $this->assertEquals($dbuserconnection, $profile->getCollector('databaseInfo')->getDatabaseUser());
        }
    }
}

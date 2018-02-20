<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FfsecondariaRepositoryFunctionalTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager()
        ;
    }

    public function testSearchBy()
    {
        $object = $this->em
                ->getRepository('FiCoreBundle:Ffsecondaria')
                ->findByDescsec('2° SECONDARIA legato al 1° record principale')
        ;

        $this->assertCount(1, $object);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

}

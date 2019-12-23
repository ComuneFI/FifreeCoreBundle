<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PermessiRepositoryFunctionalTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp() : void
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
                ->getRepository('FiCoreBundle:Permessi')
                ->findByCrud('cru')
        ;

        $this->assertCount(1, $object);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() : void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

}

<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OperatoriRepositoryFunctionalTest extends KernelTestCase
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
                ->getRepository('FiCoreBundle:Operatori')
                ->findByUsername('admin')
        ;

        $this->assertCount(1, $object);
    }

    public function testfindBySuperadmin()
    {
        $operatori = $this->em
                ->getRepository('FiCoreBundle:Ruoli')
                ->findBy(array('is_superadmin' => true));

        $this->assertCount(1, $operatori, 'Non trovato il ruolo super admin');
    }

    public function testfindruoli()
    {
        $operatori = $this->em
                ->getRepository('FiCoreBundle:Ruoli')
                ->findAll();
        $this->assertGreaterThan(0, count($operatori), 'Non trovati ruoli');
    }

    public function testfindoperatori()
    {
        $operatori = $this->em
                ->getRepository('FiCoreBundle:Operatori')
                ->findAll();
        $this->assertGreaterThan(0, count($operatori), 'Non trovati operatori');
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

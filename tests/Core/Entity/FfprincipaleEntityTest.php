<?php

namespace Fi\CoreBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FfPrincipaleRepositoryFunctionalTest extends KernelTestCase
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
        $descrizione = 'Descrizione primo record';
        $objectsearch = $this->em
                ->getRepository('FiCoreBundle:Ffprincipale')
                ->findByDescrizione($descrizione)
        ;

        $this->assertCount(1, $objectsearch);
        $this->assertEquals($descrizione, $objectsearch[0]);

        $object = $this->em
                ->getRepository('FiCoreBundle:Ffprincipale')
                ->find(1);
        $this->assertEquals(1, $object->getId());
        $this->assertEquals(5, count($object->getFfsecondarias()));

        $ffs = new \Fi\CoreBundle\Entity\Ffsecondaria();
        $ffs->setDescsec("prova");
        $object->addFfsecondaria($ffs);
        $object->removeFfsecondaria($ffs);
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

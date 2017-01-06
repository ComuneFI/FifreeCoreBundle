<?php

namespace Fi\CoreBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CoreTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
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
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /* public function testFindUserBy()
      {
      $crit = array("foo" => "bar");
      $this->repository->expects($this->once())->method('findOneBy')->with($this->equalTo($crit))->will($this->returnValue(array()));

      $this->userManager->findUserBy($crit);
      }

      public function testUpdateUser()
      {
      $user = $this->getUser();
      $this->om->expects($this->once())->method('persist')->with($this->equalTo($user));
      $this->om->expects($this->once())->method('flush');

      $this->userManager->updateUser($user);
      }

      protected function createUserManager($encoderFactory, $objectManager, $userClass)
      {
      return new UserManager($encoderFactory,  $canonicalizer, $objectManager, $userClass);
      }

      protected function getUser()
      {
      $userClass = static::USER_CLASS;

      return new $userClass();
      } */
}

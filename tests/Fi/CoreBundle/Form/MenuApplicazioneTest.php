<?php

use Symfony\Component\Form\Test\TypeTestCase;
use Fi\CoreBundle\Entity\MenuApplicazione;
use Fi\CoreBundle\Form\MenuApplicazioneType;

class MenuApplicazioneTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $menuapplicazione = "MenuApplicazione";
        $formData = array(
            'nome' => 'nome',
            'percorso' => 'percorso',
            'padre' => 4,
            'ordine' => 10,
            'attivo' => true,
            'target' => '_blank',
            'tag' => 'tag',
            'notifiche' => 'menu',
            'autorizzazionerichiesta' => 'auth',
            'percorsonotifiche' => 'percorsonotifiche'
        );

        $object = new MenuApplicazione();
        $object->setAttivo(true);
        $object->setAutorizzazionerichiesta('auth');
        $object->setNome("nome");
        $object->setNotifiche("menu");
        $object->setOrdine(10);
        $object->setPadre(4);
        $object->setPercorso("percorso");
        $object->setPercorsonotifiche("percorsonotifiche");
        $object->setTag("tag");
        $object->setTarget("_blank");

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
                ->disableOriginalConstructor()
                ->getMock();

        $router = $this->getMockBuilder('\Symfony\Bundle\FrameworkBundle\Routing\Router')
                ->disableOriginalConstructor()
                ->setMethods(['generate', 'supports', 'exists'])
                ->getMockForAbstractClass();

        $form = $this->factory->create(MenuApplicazioneType::class, $object, array('entity_manager' => $em, 'attr' => array(
                'id' => 'formdati' . $menuapplicazione,
            ),
            'action' => $router->generate($menuapplicazione . '_create'),
        ));
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        //$this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}

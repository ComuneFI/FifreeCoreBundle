<?php

namespace Fi\CoreBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Fi\CoreBundle\Entity\MenuApplicazione;
use Fi\CoreBundle\Form\MenuApplicazioneType;

class MenuApplicazioneTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $formData = array(
            'nome' => 'nome',
            'percorso' => 'percorso',
            'padre' => 'padre',
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
        $object->setPadre("padre");
        $object->setPercorso("percorso");
        $object->setPercorsonotifiche("percorsonotifiche");
        $object->setTag("tag");
        $object->setTarget("_blank");


        $form = $this->factory->create(MenuApplicazioneType::class);
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

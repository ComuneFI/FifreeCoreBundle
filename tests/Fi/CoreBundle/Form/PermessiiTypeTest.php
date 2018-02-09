<?php

use Symfony\Component\Form\Test\TypeTestCase;
use Fi\CoreBundle\Entity\Permessi;
use Fi\CoreBundle\Form\PermessiType;

class PermessiTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $ruolo = new \Fi\CoreBundle\Entity\Ruoli();
        $operatore = new \Fi\CoreBundle\Entity\Operatori();
        $formData = array(
            'modulo' => "modulo",
            'crud' => "crud",
            'operatori' => $operatore,
            'ruoli' => $ruolo,
        );

        $object = new Permessi();
        $object->setCrud("crud");
        $object->setModulo("modulo");
        $object->setOperatori($operatore);
        $object->setRuoli($ruolo);


        $form = $this->factory->create(PermessiType::class);
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

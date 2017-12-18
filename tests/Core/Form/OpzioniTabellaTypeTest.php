<?php

namespace Fi\CoreBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Fi\CoreBundle\Entity\OpzioniTabella;
use Fi\CoreBundle\Form\OpzioniTabellaType;

class OpzioniTabellaTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $tab = new \Fi\CoreBundle\Entity\Tabelle();
        $formData = array(
            'tabelle' => $tab,
            'descrizione' => "descrizione",
            'parametro' => "parametro",
            'valore' => "10",
        );

        $object = new OpzioniTabella();
        $object->setTabelle($tab);
        $object->setDescrizione("descrizione");
        $object->setParametro("parametro");
        $object->setValore(10);


        $form = $this->factory->create(OpzioniTabellaType::class);
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

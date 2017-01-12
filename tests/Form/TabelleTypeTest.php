<?php

namespace Fi\CoreBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Fi\CoreBundle\Form\TabelleType;
use Fi\CoreBundle\Entity\Tabelle;

class TabelleTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $formData = array(
            'nometabella' => 'nometabella',
            'nomecampo' => 'nomecampo',
            'mostraindex' => true,
            'ordineindex' => 10
        );


        $object = new Tabelle();
        $object->setNometabella("nometabella");
        $object->setNomecampo("nomecampo");
        $object->setMostraindex(true);
        $object->setOrdineindex(10);


        $form = $this->factory->create(TabelleType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

}

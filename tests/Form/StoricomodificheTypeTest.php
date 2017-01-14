<?php

namespace Fi\CoreBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Fi\CoreBundle\Entity\Storicomodifiche;
use Fi\CoreBundle\Form\StoricomodificheType;

class StoricomodificheTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $ope = new \Fi\CoreBundle\Entity\Operatori();
        $formData = array(
            'nometabella' => "nometabella",
            'nomecampo' => "nomecampo",
            'idtabella' => 1,
            'giorno' => "2016-01-01",
            'nometabella' => "nometabella",
            'operatori' => $ope,
            'valoreprecedente' => "prec",
        );

        $object = new Storicomodifiche();
        $object->setGiorno("2016-01-01");
        $object->setNomecampo("nomecampo");
        $object->setIdtabella(1);
        $object->setNometabella("nometabella");
        $object->setOperatori($ope);
        $object->setValoreprecedente("prec");

        $form = $this->factory->create(StoricomodificheType::class);
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

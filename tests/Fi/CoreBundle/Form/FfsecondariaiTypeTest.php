<?php

use Symfony\Component\Form\Test\TypeTestCase;
use Fi\CoreBundle\Entity\Ffsecondaria;
use Fi\CoreBundle\Form\FfsecondariaType;

class FfsecondariaTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $ffp = new \Fi\CoreBundle\Entity\Ffprincipale();
        $data = new \DateTime();
        $formData = array(
            'ffprincipale' => $ffp,
            'attivo' => true,
            'data' => $data,
            'importo' => 1000,
            'intero' => 10,
            'nota' => "nota",
        );

        $object = new Ffsecondaria();
        $object->setFfprincipale($ffp);
        $object->setAttivo(true);
        $object->setData($data);
        $object->setIntero(10);
        $object->setImporto(1000);
        $object->setNota("nota");


        $form = $this->factory->create(FfsecondariaType::class);
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

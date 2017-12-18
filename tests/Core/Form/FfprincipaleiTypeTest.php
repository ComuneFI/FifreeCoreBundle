<?php

namespace Fi\CoreBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Fi\CoreBundle\Entity\Ffprincipale;
use Fi\CoreBundle\Form\FfprincipaleType;

class FfprincipaleTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $formData = array(
            'descrizione' => "descrizione"
        );

        $object = new Ffprincipale();
        $object->setDescrizione("descrizione");

        $form = $this->factory->create(FfprincipaleType::class);
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

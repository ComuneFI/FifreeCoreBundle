<?php

namespace Fi\CoreBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Fi\CoreBundle\Entity\Ruoli;
use Fi\CoreBundle\Form\RuoliType;

class RuoliTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $formData = array(
            'ruolo' => "admin",
            'paginainiziale' => '/',
            'is_superadmin' => true,
            'is_admin' => false,
            'is_user' => false
        );

        $object = new Ruoli();
        $object->setRuolo("admin");
        $object->setPaginainiziale("/");
        $object->setIsSuperadmin(true);
        $object->setIsAdmin(false);
        $object->setIsUser(false);

        $form = $this->factory->create(RuoliType::class);
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

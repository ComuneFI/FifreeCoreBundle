<?php

namespace Fi\CoreBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class OperatoriTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $formData = array(
            'username' => "username",
            'email' => "email",
            'enabled' => true,
            'password' => "pws",
            'ruoli' => 1,
            'operatore' => 'operatore',
        );

        $type = "Fi\CoreBundle\Form\OperatoriType";
        $form = $this->factory->create($type, $formData);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($formData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

}

<?php

use Symfony\Component\Form\Test\TypeTestCase;
use Fi\CoreBundle\Form\OperatoriType;

class OperatoriTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $ruolo = new \Fi\CoreBundle\Entity\Ruoli();
        $ruolo->setRuolo("admin");
        $ruolo->setPaginainiziale("/");
        $ruolo->setIsSuperadmin(true);
        $ruolo->setIsAdmin(false);
        $ruolo->setIsUser(false);

        $formData = array(
            'username' => "username",
            'email' => "username@mail.com",
            'enabled' => true,
            'password' => "pwd",
            'ruoli' => $ruolo,
            'operatore' => 'user'
        );

        $object = new \Fi\CoreBundle\Entity\Operatori();
        $object->setUsername("username");
        $object->setEmail("username@mail.com");
        $object->setEnabled(true);
        $object->setPassword("pwd");
        $object->setOperatore("user");
        $object->setRuoli($ruolo);

        $form = $this->factory->create(OperatoriType::class);
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

<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperatoriType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('operatore')
            ->add('username')
            //->add('username_canonical')
            ->add('email')
            //->add('email_canonical')
            ->add('enabled')
            //->add('salt')
            ->add('password')
            ->add('ruoli')
            //->add('last_login')
            //->add('locked')
            //->add('expired')
            //->add('expires_at')
            //->add('confirmation_token')
            //->add('password_requested_at')
            //->add('roles')
            //->add('credentials_expired')
            //->add('credentials_expire_at')
            //->add('operatore')
        ;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'Fi\CoreBundle\Entity\Operatori',
            )
        );
    }

    public function getName()
    {
        return 'fi_corebundle_operatoritype';
    }
}

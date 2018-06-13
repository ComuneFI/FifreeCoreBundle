<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RuoliType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ruolo')
            ->add('paginainiziale')
            ->add('is_superadmin')
            ->add('is_admin')
            ->add('is_user');
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'Fi\CoreBundle\Entity\Ruoli',
            )
        );
    }

    public function getName()
    {
        return 'fi_corebundle_ruolitype';
    }
}

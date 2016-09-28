<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'Fi\CoreBundle\Entity\ruoli',
            )
        );
    }

    public function getName()
    {
        return 'fi_corebundle_ruolitype';
    }
}

<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermessiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modulo')
            ->add('crud')
            ->add('operatori')
            ->add('ruoli');
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'Fi\CoreBundle\Entity\permessi',
            )
        );
    }

    public function getName()
    {
        return 'fi_corebundle_permessitype';
    }
}

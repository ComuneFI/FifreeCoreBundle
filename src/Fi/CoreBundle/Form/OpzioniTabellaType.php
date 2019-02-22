<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpzioniTabellaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tabelle')
            ->add('descrizione')
            ->add('parametro')
            ->add('valore');
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'Fi\CoreBundle\Entity\OpzioniTabella',
            )
        );
    }

    public function getName()
    {
        return 'fi_corebundle_opzionitabellatype';
    }
}

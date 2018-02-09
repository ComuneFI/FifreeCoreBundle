<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TabelleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nometabella', null, array('label' => 'Tabella'))
            ->add('nomecampo')
            ->add('mostraindex')
            ->add('ordineindex')
            ->add('larghezzaindex')
            ->add('etichettaindex')
            ->add('mostrastampa')
            ->add('ordinestampa')
            ->add('larghezzastampa')
            ->add('etichettastampa')
            ->add('registrastorico')
            ->add('operatori');
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'Fi\CoreBundle\Entity\tabelle',
            )
        );
    }

    public function getName()
    {
        return 'fi_corebundle_tabelletype';
    }
}

<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoricomodificheType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nometabella')
            ->add('nomecampo')
            ->add('idtabella')
            ->add('giorno')
            ->add('valoreprecedente')
            ->add('operatori_id')
            ->add('operatori')
        ;
    }
    
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fi\CoreBundle\Entity\Storicomodifiche'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fi_corebundle_storicomodifiche';
    }
}

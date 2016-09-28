<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AllegatiType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nometabella', 'hidden')
            ->add('indicetabella', 'hidden')
            ->add('allegato')
            ->add('allegatofile', 'file', array('data_class' => null));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'Fi\CoreBundle\Entity\allegati',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fi_corebundle_allegati';
    }
}

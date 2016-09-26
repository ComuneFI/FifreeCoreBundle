<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FfsecondariaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descsec')
            ->add('ffprincipale')
            ->add('data')
            ->add('intero')
            ->add('importo')
            ->add('nota')
            ->add('attivo')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fi\CoreBundle\Entity\Ffsecondaria',
        ));
    }

    public function getName()
    {
        return 'fi_corebundle_ffsecondariatype';
    }
}

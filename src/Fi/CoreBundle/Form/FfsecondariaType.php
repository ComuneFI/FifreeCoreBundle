<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FfsecondariaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('descsec', null, array(
                    'attr' => array(
                        'class' => 'accessostorico'
                    )
                        ))
                ->add('ffprincipale', null, array(
                    'attr' => array(
                        'class' => 'accessostorico'
                    )
                        ))
                ->add('data')
                ->add('intero')
                ->add('importo')
                ->add('nota')
                ->add('attivo');
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                    'data_class' => 'Fi\CoreBundle\Entity\Ffsecondaria',
                )
        );
    }

    public function getName()
    {
        return 'fi_corebundle_ffsecondariatype';
    }
}

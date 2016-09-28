<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuApplicazioneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nome')
            ->add('percorso')
            ->add('padre')
            ->add('ordine')
            ->add('attivo')
            ->add('target')
            ->add('tag')
            ->add('notifiche')
            ->add('autorizzazionerichiesta')
            ->add('percorsonotifiche');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'Fi\CoreBundle\Entity\menuApplicazione',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fi_corebundle_menuapplicazione';
    }
}

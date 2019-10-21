<?php

namespace Fi\CoreBundle\Form;

use IntlDateFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FfsecondariaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $giornidellasettimana = array();
        $format = new IntlDateFormatter('it_IT', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, "EEEE");
        for ($index = 1; $index < 8; $index++) {
            $giornidellasettimana[ucfirst($format->format(strtotime('next Sunday +' . $index . ' days')))] = $index;
        }
        
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
                ->add('giornodellasettimana', ChoiceType::class, array(
                    'choices' => $giornidellasettimana
                        ))
                ->add('data')
                /*->add('data', DateTimeType::class, array(
                    'input' => 'datetime',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy HH:mm',
                    'attr' => array('class' => 'ficorebundle_datetimepicker'),
                    'required' => true,
                    'label' => 'Data ora'))*/
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

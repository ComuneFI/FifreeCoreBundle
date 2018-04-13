<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MenuApplicazioneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];

        $builder
                ->add('nome')
                ->add('percorso')
                ->add("padre", ChoiceType::class, array("label" => "Padre",
                    "choices" => $this->getAllMenuApplicazione($em)
                ))
                ->add('ordine')
                ->add('attivo')
                ->add('target')
                ->add('tag')
                ->add('notifiche')
                ->add('autorizzazionerichiesta')
                ->add('percorsonotifiche');
    }
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                    'data_class' => 'Fi\CoreBundle\Entity\MenuApplicazione',
                )
        );
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('entity_manager');
    }
    private function getAllMenuApplicazione($em)
    {
        $menus = array();
        if ($em->getRepository('FiCoreBundle:MenuApplicazione')) {
            $results = $em->getRepository('FiCoreBundle:MenuApplicazione')->findBy([], ['nome' => 'ASC']);

            $menus = array("" => null);
            foreach ($results as $bu) {
                $menus[$bu->getNome()] = $bu->getId();
            }
        }

        return $menus;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'fi_corebundle_menuapplicazione';
    }
}

<?php

namespace OGIVE\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use OGIVE\ProjectBundle\Form\SubSubTaskType;

class SubTaskType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nom', TextType::class, array('required' => false))
                ->add('numero', null, array('required' => false))
                ->add('unite', null, array('required' => false))
                ->add('prixUnitaire', null, array('required' => false))
                ->add('qtePrevueMarche', null, array('required' => false))
                ->add('qtePrevueProjetExec', null, array('required' => false))
                ->add('qteCumulMoisPrec', null, array('required' => false))
                ->add('qteMois', null, array('required' => false))
                ->add('qteCumulMois', null, array('required' => false))
                ->add('startDate', TextType::class, array('required' => false))
                ->add('endDate', TextType::class, array('required' => false))
                ->add('description', null, array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'OGIVE\ProjectBundle\Entity\Task'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'ogive_projectbundle_sub_task';
    }

}

<?php

namespace OGIVE\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;

class ProjectType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('numeroMarche', null, array('required' => false))
                ->add('subject', null, array('required' => false))
                ->add('projectCost', null, array('required' => false))
                ->add('projectCostCurrency', CurrencyType::class , array('placeholder' => 'Selectionner la dévise','required' => false))
                ->add('numeroLot', null, array('required' => false))
                ->add('lieuExecution', null, array('required' => false))
                ->add('region', null, array('required' => false))
                ->add('departement', null, array('required' => false))
                ->add('region', null, array('required' => false))                
                ->add('lots', CollectionType::class, array(
                    'entry_type' => LotType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'allow_delete' => true
                ))
                ->add('owners', CollectionType::class, array(
                    'entry_type' => OwnerType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'allow_delete' => true
                ))
                ->add('projectManagers', CollectionType::class, array(
                    'entry_type' => ProjectManagerType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'allow_delete' => true
                ))
                ->add('holders', CollectionType::class, array(
                    'entry_type' => HolderType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'allow_delete' => true
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'OGIVE\ProjectBundle\Entity\Project'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'ogive_projectbundle_project';
    }

}

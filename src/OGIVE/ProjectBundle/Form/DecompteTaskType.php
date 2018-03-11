<?php

namespace OGIVE\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class DecompteTaskType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nom', HiddenType::class, array('required' => false))
                ->add('numero', HiddenType::class, array('required' => false))
                ->add('unite', HiddenType::class, array('required' => false))
                ->add('prixUnitaire', HiddenType::class, array('required' => false))
                ->add('qtePrevueMarche', HiddenType::class, array('required' => false))
                ->add('qtePrevueProjetExec', HiddenType::class, array('required' => false))
                ->add('qteCumulMoisPrec', HiddenType::class, array('required' => false))
                ->add('qteMois', null, array('required' => false))
                ->add('qteCumulMois', HiddenType::class, array('required' => false))
                ->add('mtPrevueMarche', HiddenType::class, array('required' => false))
                ->add('mtPrevueProjetExec', HiddenType::class, array('required' => false))
                ->add('mtCumulMoisPrec', HiddenType::class, array('required' => false))
                ->add('mtMois', HiddenType::class, array('required' => false))
                ->add('mtCumulMois', HiddenType::class, array('required' => false))
                ->add('pourcentRealisation', HiddenType::class, array('required' => false))
                ->add('startDate', HiddenType::class, array('required' => false))
                ->add('endDate', HiddenType::class, array('required' => false))
                ->add('description', HiddenType::class, array('required' => false))
                ->add('subDecompteTasks', CollectionType::class, array(
                    'entry_type' => SubDecompteTaskType::class,
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
            'data_class' => 'OGIVE\ProjectBundle\Entity\DecompteTask'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'ogive_projectbundle_decomptetask';
    }

}

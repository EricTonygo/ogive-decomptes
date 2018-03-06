<?php

namespace OGIVE\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OtherContributorType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('contributorType', TextType::class, array('required' => false))
                ->add('nom', TextType::class, array('required' => false))
                ->add('email', TextType::class, array('required' => false))  
                ->add('phone', TextType::class, array('required' => false))  
                ->add('codePostal', TextType::class, array('required' => false))            
                ->add('faxNumber', TextType::class, array('required' => false))
                ->add('rc', TextType::class, array('required' => false))
                ->add('numeroContribuable', TextType::class, array('required' => false))
                ->add('numeroCompteBancaire', TextType::class, array('required' => false))
                ->add('nomBanque', TextType::class, array('required' => false))
                ->add('intitule', TextType::class, array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'OGIVE\ProjectBundle\Entity\OtherContributor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'ogive_projectbundle_serviceProvider';
    }

}

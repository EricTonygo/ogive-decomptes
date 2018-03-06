<?php

namespace OGIVE\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ServiceProviderType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
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
            'data_class' => 'OGIVE\ProjectBundle\Entity\ServiceProvider'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'ogive_projectbundle_serviceProvider';
    }

}

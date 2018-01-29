<?php

namespace OGIVE\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class HolderType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nom', null, array('required' => false))
                ->add('email', null, array('required' => false))  
                ->add('phone', null, array('required' => false))  
                ->add('codePostal', null, array('required' => false))            
                ->add('fax', null, array('required' => false))
                ->add('rc', null, array('required' => false))
                ->add('numeroContribuable', null, array('required' => false))
                ->add('numeroCompteBancaire', null, array('required' => false))
                ->add('nomBanque', null, array('required' => false))
                ->add('intitule', null, array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'OGIVE\ProjectBundle\Entity\Holder'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'ogive_projectbundle_holder';
    }

}

<?php

namespace OGIVE\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OGIVE\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class ProjectManagerType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
//                ->add('nom', TextType::class, array('required' => false))
//                ->add('email', TextType::class, array('required' => false))
                ->add('user', EntityType::class, array(
                    'class' => User::class,
                    'choice_label' => 'username',
                    'placeholder' => "Rechercher par nom d'utilisateur",
                    'multiple' => false,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
//                                ->where('u.enabled = :enabled')
                                ->orderBy('u.username', 'ASC');
//                                ->setParameter('enabled', true);
                    }
                ))
                ->add('phone', TextType::class, array('required' => false))
                ->add('codePostal', TextType::class, array('required' => false))
                ->add('faxNumber', TextType::class, array('required' => false))
                ->add('rc', TextType::class, array('required' => false))
                ->add('numeroContribuable', TextType::class, array('required' => false))
                ->add('numeroCompteBancaire', TextType::class, array('required' => false))
                ->add('nomBanque', TextType::class, array('required' => false))
                ->add('intitule', TextType::class, array('required' => false))
                ->add('ordrePriorite', null, array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'OGIVE\ProjectBundle\Entity\ProjectManager'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'ogive_projectbundle_projectManager';
    }

}

<?php

namespace OGIVE\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OGIVE\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class ProjectOwnerType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
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
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'OGIVE\ProjectBundle\Entity\Owner'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'ogive_projectbundle_project_owner';
    }

}

<?php

namespace OGIVE\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('lastname', null, array(
                'required' => false
            ))
//            ->add('firstname', null, array(
//                'required' => false
//            ))
//            ->add('file', FileType::class ,array(
//                'attr' => array('class'=>'inputfile'),
//                'required' => false
//            ))
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle', 'required'=> false))
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'required'=> false))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
                'required'=> false
            ))
                ;
           /*->add('roles', ChoiceType::class, array(
                        'label' => 'Privileges',
                       'multiple' => true,
                       'expanded' => true,
                       'choices' => array(
                           'Super Administrateur' => 'ROLE_SUPER_ADMIN',
                           'Administrateur' => 'ROLE_ADMIN',
                           'Redacteur' => 'REDACTEUR',
                           'Facturier' => 'FACTURIER'
                   )
               )
           );*/
        
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OGIVE\UserBundle\Entity\User'
        ));
    }
    
     public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}

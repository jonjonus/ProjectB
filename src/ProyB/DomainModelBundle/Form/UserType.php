<?php

namespace ProyB\DomainModelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'text', array('empty_data'  => null,
                                        'required' => false,//TODO hacer requerido en HTML5?
                                        )
                )
            ->add('password', 'password', array('empty_data'  => null,
                                                'required' => false,//TODO hacer requerido en HTML5?
                                                'label' => 'Current Password',
                                                )
                )
            ->add('newPassword', 'repeated', array('type' => 'password',
                                                'invalid_message' => 'The password fields must match.',
                                                'mapped' => false,
                                                'empty_data'  => null,
                                                'required' => false,//TODO hacer requerido en HTML5?
                                                'first_options'  => array('label' => 'New Password'),
                                                'second_options' => array('label' => 'Repeat Password'),
                                                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProyB\DomainModelBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'proyb_domainmodelbundle_user';
    }
}

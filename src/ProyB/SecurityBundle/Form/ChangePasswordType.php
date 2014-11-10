<?php

namespace ProyB\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChangePasswordType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword','password',array('required' => false,
                                        )
                )
            ->add('newPassword', 'repeated', array('type' => 'password',
                                                'required' => false,
                                                'invalid_message' => 'The password fields must match.',
                                                'first_options'  => array('label' => 'New Password'),
                                                'second_options' => array('label' => 'Repeat Password'),
                                                )
                );    
    }
        
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProyB\SecurityBundle\Form\Model\ChangePassword'
        ));
    }

    public function getName()
    {
        return 'proyb_securitybundle_change_password';
    }
}

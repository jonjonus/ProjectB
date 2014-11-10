<?php

namespace ProyB\DomainModelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password')
            ->add('name')
            ->add('email')
            ->add('states', 'entity', array('class' => 'ProyBDomainModelBundle:State',
                                            'property' => 'description',
                                            'query_builder' => function(\ProyB\DomainModelBundle\Entity\StateRepository $er) {
                                                                 return $er->buildQueryAllStates();
                                                            },
                                            'required'    => false,
                                            'empty_value' => 'Select States',
                                            'empty_data'  => null,
                                            'multiple'    => true,
                                            'expanded'    => false,
                                            'by_reference' => false)
                );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProyB\DomainModelBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'proyb_domainmodelbundle_user';
    }
}

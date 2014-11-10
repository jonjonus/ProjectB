<?php

namespace ProyB\DomainModelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StateType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('users', 'entity', array('class' => 'ProyBDomainModelBundle:User',
                                                'property' => 'username',
                                                'query_builder' => function(\ProyB\DomainModelBundle\Entity\UserRepository $er) {
                                                                     return $er->buildQueryAllUsers();
                                                                },
                                                'required'    => false,
                                                'empty_value' => 'Select Users',
                                                'empty_data'  => null,
                                                'multiple'    => true,
                                                'expanded'    => false,
                                                'by_reference' => false)
                )
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProyB\DomainModelBundle\Entity\State'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'proyb_domainmodelbundle_state';
    }
}

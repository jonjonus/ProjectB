<?php

namespace ProyB\DomainModelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminTransactionType extends AbstractType
{   
    private $states;
    
    public function __construct($states){
        //parent::__construct();
        $this->states = $states;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden', array('required'=>false,
                                        )
                    )
            ->add('state', 'entity', array('class' => 'ProyBDomainModelBundle:State',
                                            'property' => 'description',
                                            'choices' => $this->states,
                                            'empty_value' => 'Select State',
                                            'empty_data'  => null,
                                            'required' => false,//TODO hacer requerido en HTML5?
                                            )
                )
            ->add('date', 'date', array('widget' => 'single_text',
                                        'required' => false,//TODO hacer requerido en HTML5?
                                        'input' => 'datetime',
                                        'format' => 'MM/dd/yyyy',
                                        )
                )
            ->add('amount', 'number', array('required' => false,//TODO hacer requerido en HTML5?
                                            )
                )
            ->add('comment', 'textarea', array ('required' => false,
                                                )
                )
            ->add('insertDate', 'datetime', array('widget' => 'single_text',
                                                'required' => false,//TODO hacer requerido en HTML5?
                                                'input' => 'datetime',
                                                'format' => 'MM/dd/yyyy hh:mm:ss a',
                                                )
                )
            ->add('insertUser', 'entity', array('class' => 'ProyBDomainModelBundle:User',
                                                'property' => 'username',
                                                'required' => false,//TODO hacer requerido en HTML5?
                                                'empty_value' => 'Select User',
                                                'empty_data'  => null,
                                                'query_builder' => function(\ProyB\DomainModelBundle\Entity\UserRepository $er) {
                                                                     return $er->buildQueryAllUsers();
                                                                },
                                                )
                )
            ->add('updateDate', 'datetime', array('widget' => 'single_text',
                                                'required' => false,//TODO hacer requerido en HTML5?
                                                'input' => 'datetime',
                                                'format' => 'MM/dd/yyyy hh:mm:ss a',
                                                )
                )
            ->add('updateUser', 'entity', array('class' => 'ProyBDomainModelBundle:User',
                                                'property' => 'username',
                                                'required' => false,//TODO hacer requerido en HTML5?
                                                'empty_value' => 'Select User',
                                                'empty_data'  => null,
                                                'query_builder' => function(\ProyB\DomainModelBundle\Entity\UserRepository $er) {
                                                                     return $er->buildQueryAllUsers();
                                                                },
                                                )
                )
            ->add('inactiveDate', 'datetime', array('widget' => 'single_text',
                                                'required' => false,//TODO hacer requerido en HTML5?
                                                'input' => 'datetime',
                                                'format' => 'MM/dd/yyyy hh:mm:ss a',
                                                )
                )
            ->add('inactiveUser', 'entity', array('class' => 'ProyBDomainModelBundle:User',
                                                'property' => 'username',
                                                'query_builder' => function(\ProyB\DomainModelBundle\Entity\UserRepository $er) {
                                                                     return $er->buildQueryAllUsers();
                                                                },
                                                'required'    => false,
                                                'empty_value' => 'Select User',
                                                'empty_data'  => null,
                                                )
            
                )
            ->add('count', 'number', array('required' => false,//TODO hacer requerido en HTML5?
                                            'read_only' => true,
                                            'mapped' => false,
                                            )
                );
    //TODO buscar forma de agregar atributos comunes a todos juntos en lugar de uno por uno (se pueden recorrer y mergear)
    //http://stackoverflow.com/questions/16466370/symfony2-formbuilder-how-to-add-and-attribute-to-multiple-form-fields
    }
    
     public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProyB\DomainModelBundle\Entity\Transaction'
        ));
    }

    
    public function getName()
    {
        return 'proyb_domainmodelbundle_transaction';
    }
}
<?php

namespace ProyB\DomainModelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TransactionType extends AbstractType
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
//                                            'empty_data'  => null,
                                            'required' => false,//TODO hacer requerido en HTML5?
                                            )
                )
            ->add('date', 'date', array('widget' => 'single_text',
                                        'input' => 'datetime',
                                        'empty_data'  => null,
                                        'format' => 'MM/dd/yyyy',
                                        'required' => false,//TODO hacer requerido en HTML5?
                                        )
                )
            ->add('amount', 'number', array('required' => false,//TODO hacer requerido en HTML5?
                                            )
                )
            ->add('comment', 'textarea', array ('required' => false,
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

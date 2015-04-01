<?php

namespace Mci\Bundle\PatientBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StatusType extends AbstractType
{
    private $patientStatus;
    public function __construct($patientStatus){
        $this->patientStatus =$patientStatus;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('type', 'choice', array(
                    'choices' =>$this->patientStatus,
                    'attr' => array('class' => 'form-control'),
                    'required'  => true,
                    'label' => 'Patient Status'
                )
            )

            ->add('date_of_death', 'text', array(
                'attr' => array('class' => 'form-control datepicker_common  datepicker_hide'),
                'required'  => false
            ));
    }

    public function getName()
    {
        return 'mci_bundle_patientBundle_patients_status';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mci\Bundle\PatientBundle\FormMapper\Status'
        ));
    }
}

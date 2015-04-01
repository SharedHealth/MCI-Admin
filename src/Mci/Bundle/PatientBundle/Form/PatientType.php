<?php

namespace Mci\Bundle\PatientBundle\Form;

use Mci\Bundle\PatientBundle\Services\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mci\Bundle\PatientBundle\Services\MasterData;

class PatientType extends AbstractType
{

    private $patientObject;
    private $location;
    private $masterData;


    public function __construct(Location $location,MasterData $masterData,$object){
        $this->location = $location;
        $this->masterData = $masterData;
        $this->patientObject = $object;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $presentAddress =  $this->patientObject->getPresentAddress();
        $permanentAddress =  property_exists($this->patientObject,'permanent_address') ? $this->patientObject->getPermanentAddress():null;
        $gender =  $this->masterData->getAllByType('gender');
        $ethnicity = array();
        $religion = $this->masterData->getAllByType('religion');
        $bloodGroup = $this->masterData->getAllByType('blood_group');
        $eduLevel = $this->masterData->getAllByType('education_level');
        $occupation = $this->masterData->getAllByType('occupation');
        $disability = $this->masterData->getAllByType('disability');
        $maritalStatus = $this->masterData->getAllByType('marital_status');
        $patientStatus = $this->masterData->getAllByType('status');
        $builder
            ->add('nid', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('uid', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('bin_brn', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('sur_name', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('given_name', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('name_bangla', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('household_code', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('place_of_birth', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('nationality', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('primary_contact', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('date_of_birth', 'text', array(
                'attr' => array('class' => 'form-control datepicker_common  datepicker_hide')
            ))
            ->add('gender', 'choice',
                array(
                    'choices' =>$gender,
                    'attr' => array('class' => 'form-control')
                )
            )
            ->add('ethnicity', 'choice',
                array(
                    'choices' =>$ethnicity,
                    'attr' => array('class' => 'form-control','disabled'=>'disabled'),
                    'empty_value' => '--Please select--',
                    'required'  => false
                )
            )
            ->add('religion', 'choice',
                array(
                    'choices' =>$religion,
                    'attr' => array('class' => 'form-control'),
                    'empty_value' => '--Please select--',
                    'required'  => false
                )
            )
            ->add('blood_group', 'choice',
                array(
                    'choices' =>$bloodGroup,
                    'attr' => array('class' => 'form-control'),
                    'empty_value' => '--Please select--',
                    'required'  => false
                )
            )
            ->add('occupation', 'choice',
                array(
                    'choices' =>$occupation,
                    'attr' => array('class' => 'form-control'),
                    'empty_value' => '--Please select--',
                    'required'  => false
                )
            )
            ->add('edu_level', 'choice',
                array(
                    'choices' =>$eduLevel,
                    'attr' => array('class' => 'form-control'),
                    'empty_value' => '--Please select--',
                    'required'  => false
                )
            )
            ->add('disability', 'choice',
                array(
                    'choices' =>$disability,
                    'attr' => array('class' => 'form-control'),
                    'empty_value' => '--Please select--',
                    'required'  => false
                )
            )
            ->add('marital_status', 'choice', array(
                    'choices' =>$maritalStatus,
                    'attr' => array('class' => 'form-control'),
                    'empty_value' => '--Please select--',
                    'required'  => false
                )
            )

            ->add('confidential', 'choice', array(
                    'choices' =>array('No' =>'No','Yes'=>'Yes'),
                    'attr' => array('class' => 'form-control'),
                    'required'  => true
                )
            )

            ->add('present_address', new AddressType($this->masterData,$this->location,$presentAddress))
            ->add('permanent_address', new AddressType($this->masterData,$this->location,$permanentAddress))
            ->add('phone_number', new ContactType())
            ->add('primary_contact_number', new ContactType())
            ->add('status', new StatusType($patientStatus))
            ->add('relations', 'collection', array(
                'type' => new RelationType($this->masterData),
                'allow_add' => true,
                'label' => false
            ))
            ->add('save', 'submit', array(
                'attr' => array('class' => 'btn btn-primary form-group'),
            ));
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mci\Bundle\PatientBundle\FormMapper\Patient'
        ));
    }

    public function getName()
    {
        return 'mci_bundle_patientBundle_patients';
    }

}

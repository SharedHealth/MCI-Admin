<?php

namespace Mci\Bundle\PatientBundle\Form;

use Mci\Bundle\PatientBundle\FormMapper\Relation;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PatientType extends AbstractType
{

    private $serviceContainer;
    private $patientObject;

    public function __construct(Container $container,$object){
        $this->serviceContainer = $container;
        $this->patientObject = $object;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $presentAddress =  $this->patientObject->getPresentAddress();
        $permanentAddress =  property_exists($this->patientObject,'permanent_address') ? $this->patientObject->getPermanentAddress():null;
        $gender =  (array)json_decode(file_get_contents('assets/json/gender.json'));

        $ethnicity = array();
        $religion = $this->getArrayFromJson('assets/json/religion.json');
        $bloodGroup = $this->getArrayFromJson('assets/json/bloodGroup.json');
        $eduLevel = $this->getArrayFromJson('assets/json/eduLevel.json');
        $occupation = $this->getArrayFromJson('assets/json/occupation.json');
        $disability = $this->getArrayFromJson('assets/json/disability.json');
        $maritalStatus = $this->getArrayFromJson('assets/json/relationStat.json');
        $patientStatus = $this->getArrayFromJson('assets/json/livingStatus.json');
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
            ->add('status', 'choice', array(
                    'choices' =>$patientStatus,
                    'attr' => array('class' => 'form-control'),
                    'empty_value' => '--Please select--',
                    'required'  => false,
                    'label' => 'Patient Status'
                )
            )
            ->add('confidential', 'choice', array(
                    'choices' =>array('No' =>'No','Yes'=>'Yes'),
                    'attr' => array('class' => 'form-control'),
                    'required'  => true
                )
            )
            ->add('date_of_death', 'text', array(
                'attr' => array('class' => 'form-control datepicker_common datepicker_hide'),
                'required'  => false
            ))
            ->add('present_address', new AddressType($this->serviceContainer,$presentAddress))
            ->add('permanent_address', new AddressType($this->serviceContainer,$permanentAddress))
            ->add('phone_number', new ContactType())
            ->add('primary_contact_number', new ContactType())
            ->add('relations', 'collection', array(
                'type' => new RelationType(),
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

    /**
     * @return array
     */
    public function getArrayFromJson($url)
    {
        return (array)json_decode(file_get_contents($url));
    }
}

<?php

namespace Mci\Bundle\PatientBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $gender = array();
        $ethnicity = array();
        $religion = array();
        $bloodGroup = array();
        $eduLevel = array();
        $occupation = array();
        $disability = array();
        $maritalStatus = array();
        $builder
           ->add('nid', 'text',array(
                'data' => 'Default value'
             ))
            ->add('uid', 'text')
            ->add('bin_brn', 'text')
            ->add('sur_name', 'text')
            ->add('given_name', 'text')
            ->add('name_bangla', 'text')
            ->add('place_of_birth', 'text')
            ->add('nationality', 'text')
            ->add('primary_contact', 'text')
            ->add('date_of_birth', 'text')
            ->add('gender', 'choice',$gender)
            ->add('ethnicity', 'choice',$ethnicity)
            ->add('religion', 'choice',$religion)
            ->add('blood_group', 'choice',$bloodGroup)
            ->add('occupation', 'choice',$occupation)
            ->add('edu_level', 'choice',$eduLevel)
            ->add('disability', 'choice',$disability)
            ->add('marital_status', 'choice',$maritalStatus)
            ->add('present_address', new AddressType($data = array(2,4,5)))
            ->add('permanent_address', new AddressType($data = array(2,4,5)))
            ->add('phone_number', new ContactType($data = array(2,4,5)))
            ->add('primary_contact_number', new ContactType($data = array(2,4,5)))
            ->add('relation', new RelationType($data = array(2,4,5)));
    }


    public function getName()
    {
        return 'mci_bundle_patientBundle_patients';
    }
}

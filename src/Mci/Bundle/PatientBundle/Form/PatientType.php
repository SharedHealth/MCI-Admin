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
            ->add('nid', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('uid', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('bin_brn', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('sur_name', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('given_name', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('name_bangla', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('place_of_birth', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('nationality', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('primary_contact', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('date_of_birth', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('gender', 'choice',
                array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $gender
            )
            ->add('ethnicity', 'choice',
                array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $ethnicity
            )
            ->add('religion', 'choice',
                array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $religion
            )
            ->add('blood_group', 'choice',
                array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $bloodGroup
            )
            ->add('occupation', 'choice',
                array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $occupation
            )
            ->add('edu_level', 'choice',
                array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $eduLevel
            )
            ->add('disability', 'choice',
                array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $disability
            )
            ->add('marital_status', 'choice', array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $maritalStatus
            )
            ->add('present_address', new AddressType($data = array(2, 4, 5)))
            ->add('permanent_address', new AddressType($data = array(2, 4, 5)))
            ->add('phone_number', new ContactType($data = array(2, 4, 5)))
            ->add('primary_contact_number', new ContactType($data = array(2, 4, 5)))
            ->add('relation', 'collection', array(
                'type'         => new RelationType($data = array(2,4,5)),
                'allow_add'    => true,
            ))
            ->add('save', 'button', array(
                'attr' => array('class' => 'btn btn-primary form-group'),
             ));
        //  ->add('relation', new RelationType($data = array(2,4,5)));
    }

    public function getName()
    {
        return 'mci_bundle_patientBundle_patients';
    }
}

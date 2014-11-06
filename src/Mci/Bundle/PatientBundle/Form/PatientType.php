<?php

namespace Mci\Bundle\PatientBundle\Form;

use Mci\Bundle\PatientBundle\FormMapper\Relation;
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
                'attr' => array('class' => 'form-control')
            ))
            ->add('uid', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('bin_brn', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('sur_name', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('given_name', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('name_bangla', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('place_of_birth', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('nationality', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('primary_contact', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('date_of_birth', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('gender', 'choice',
                array(
                    'attr' => array('class' => 'form-control')
                ),
                $gender
            )
            ->add('ethnicity', 'choice',
                array(
                    'attr' => array('class' => 'form-control')
                ),
                $ethnicity
            )
            ->add('religion', 'choice',
                array(
                    'attr' => array('class' => 'form-control')
                ),
                $religion
            )
            ->add('blood_group', 'choice',
                array(
                    'attr' => array('class' => 'form-control')
                ),
                $bloodGroup
            )
            ->add('occupation', 'choice',
                array(
                    'attr' => array('class' => 'form-control')
                ),
                $occupation
            )
            ->add('edu_level', 'choice',
                array(
                    'attr' => array('class' => 'form-control')
                ),
                $eduLevel
            )
            ->add('disability', 'choice',
                array(
                    'attr' => array('class' => 'form-control')
                ),
                $disability
            )
            ->add('marital_status', 'choice', array(
                    'attr' => array('class' => 'form-control')
                ),
                $maritalStatus
            )
            ->add('present_address', new AddressType())
            ->add('permanent_address', new AddressType())
            ->add('phone_number', new ContactType())
            ->add('primary_contact_number', new ContactType())
            ->add('relations', 'collection', array(
                'type' => new RelationType(),
                'allow_add' => true,
            ))
            ->add('save', 'button', array(
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

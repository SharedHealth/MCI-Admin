<?php

namespace Mci\Bundle\PatientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{
    public $data;

    public function __construct($address)
    {
        $this->data = $address;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $division = array();
        $district = array();
        $union = array();
        $upazilla = array();
        $cityCorporation = array();
        $countryCode = array();

        $builder
            ->add('address_line', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('holding_number', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('street', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('area_mouja', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('village', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('post_office', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('post_code', 'text', array(
                'data' => 'Default value',
                'attr' => array('class' => 'form-control')
            ))
            ->add('division_id', 'choice', array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $division
            )
            ->add('district_id', 'choice', array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $district
            )
            ->add('union_id', 'choice', array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $union
            )
            ->add('upazilla_id', 'choice', array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $upazilla
            )
            ->add('city_corporation_id', 'choice', array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $cityCorporation
            )
            ->add('country_code', 'choice', array(
                    'data' => 'Default value',
                    'attr' => array('class' => 'form-control')
                ),
                $countryCode
            );
    }

    public function getName()
    {
        return 'mci_bundle_patientBundle_patients_present_address';
    }
}

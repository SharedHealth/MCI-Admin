<?php

namespace Mci\Bundle\PatientBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{
    public $data;

    public function __construct($address){
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
        ->add('address_line','text')
        ->add('holding_number','text')
        ->add('street','text')
        ->add('area_mouja','text')
        ->add('village','text')
        ->add('post_office','text')
        ->add('post_code','text')
        ->add('division_id', 'choice',$division)
        ->add('district_id', 'choice',$district)
        ->add('union_id', 'choice',$union)
        ->add('upazilla_id', 'choice',$upazilla)
        ->add('city_corporation_id', 'choice',$cityCorporation)
        ->add('country_code', 'choice',$countryCode);
    }

    public function getName()
    {
        return 'mci_bundle_patientBundle_patients_present_address';
    }
}

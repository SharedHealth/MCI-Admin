<?php

namespace Mci\Bundle\PatientBundle\Form;

use Mci\Bundle\PatientBundle\Form\Type\LocationType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{
    private $serviceContainer;
    private $addressObject;

    public function __construct( Container $container,  $object){
        $this->serviceContainer = $container;
        $this->addressObject = $object;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locationService = $this->serviceContainer->get('mci.location');
        $divisions = $locationService->prepairFormData($locationService->getLocation());
        $districts = array();
        $upazilas = array();
        $citycorporations = array();
        $unions = array();
        $wards = array();

        if($this->addressObject){
            $divisionId = $this->addressObject->getDivisionId();
            $districtId = $this->addressObject->getDistrictId();
            $upazilaId = $this->addressObject->getUpazilaId();
            $cityCorporationId = $this->addressObject->getCityCorporationId();
            $unionId = $this->addressObject->getUnionOrurbanwardId();
            $wardId = $this->addressObject->getRuralWardId();
            $districts = $locationService->prepairFormData($locationService->getLocation($divisionId));
            $upazilas = $locationService->prepairFormData($locationService->getLocation($divisionId.$districtId));

            if($upazilaId){
                $citycorporations = $locationService->prepairFormData($locationService->getLocation($divisionId.$districtId.$upazilaId));
            }

            if($cityCorporationId){
                $unions = $locationService->prepairFormData($locationService->getLocation($divisionId.$districtId.$upazilaId.$cityCorporationId));
            }

            if($unionId){
                $wards = $locationService->prepairFormData($locationService->getLocation($divisionId.$districtId.$upazilaId.$cityCorporationId.$unionId));
            }
        }

        $countryCode = $this->getArrayFromJson('assets/json/countryCode.json');

        $builder
            ->add('address_line', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('holding_number', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('street', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('area_mouja', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('village', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('post_office', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('post_code', 'text', array(
                'attr' => array('class' => 'form-control'),
                'required'  => false
            ))
            ->add('division_id', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' => $divisions,
                    'empty_value' => '--Please select--',
                    'required'  => false
                )
            )
            ->add('district_id', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' => $districts,
                    'empty_value' => '--Please select--',
                     'required'  => false
                )
            )

            ->add('upazila_id', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' => $upazilas,
                    'empty_value' => '--Please select--',
                     'required'  => false
                )
            )
            ->add('city_corporation_id', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' =>  $citycorporations,
                    'empty_value' => '--Please select--',
                    'required'  => false,

                )
            )
            ->add('union_or_urban_ward_id', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'required'  => false,
                    'choices' => $unions,
                    'empty_value' => '--Please select--'

                )
            )
            ->add('rural_ward_id', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' => $wards,
                    'empty_value' => '--Please select--',
                    'required'  => false,

                )
            )

            ->add('country_code', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' => $countryCode,
                    'required'  => true,
                    'preferred_choices' => array('050')
                )
            );
    }

    public function getName()
    {
        return 'mci_bundle_patientBundle_patients_present_address';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mci\Bundle\PatientBundle\FormMapper\Address'
        ));
    }

    /**
     * @return array
     */
    public function getArrayFromJson($url)
    {
        return (array)json_decode(file_get_contents($url));
    }
}

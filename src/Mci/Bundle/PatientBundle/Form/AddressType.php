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
        $divisions = $locationService->getAllDivision();
        $union = array();
        $divisionId = $locationService->getDivisionId($this->addressObject->getDivisionId());
        $districts = $locationService->getAllDistrict($divisionId);
        $districtId = $locationService->getDistictId($this->addressObject->getDistrictId());
        $upazillas = $locationService->getAllUpazilla($districtId);
        $cityCorporation = array();
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
                'attr' => array('class' => 'form-control')
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
                    'empty_value' => '--Please select--'
                )
            )
            ->add('district_id', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' => $districts,
                    'empty_value' => '--Please select--'
                )
            )
            ->add('union_id', 'choice', array(
                    'attr' => array('class' => 'form-control')
                )
            )
            ->add('upazilla_id', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' => $upazillas,
                    'empty_value' => '--Please select--'
                )
            )
            ->add('city_corporation_id', 'choice', array(
                    'attr' => array('class' => 'form-control')
                ),
                $cityCorporation
            )
            ->add('country_code', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' => $countryCode,
                    'required'  => false,
                    'empty_value' => '--Please select--'
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

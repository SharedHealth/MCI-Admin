<?php

namespace Mci\Bundle\PatientBundle\Form;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{
    private $serviceContainer;

    public function __construct( Container $container){
        $this->serviceContainer = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locationService = $this->serviceContainer->get('mci.location');
        $divisions = $locationService->getAllDivision();

      //  $district = $this->getArrayFromJson('assets/json/district.json');
        $union = array();
        $districts = $locationService->getAllDistrict();
        $upazilla = array();
        // $upazilla = $this->getArrayFromJson('assets/json/upazilla.json');
        $cityCorporation = array();
        $countryCode = array();

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
                    'choices' => $divisions
                )
            )
            ->add('district_id', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' => $districts
                )
            )
            ->add('union_id', 'choice', array(
                    'attr' => array('class' => 'form-control')
                )
            )
            ->add('upazilla_id', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'choices' => $upazilla
                )
            )
            ->add('city_corporation_id', 'choice', array(
                    'attr' => array('class' => 'form-control')
                ),
                $cityCorporation
            )
            ->add('country_code', 'choice', array(
                    'attr' => array('class' => 'form-control'),
                    'required'  => false
                ),
                $countryCode
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

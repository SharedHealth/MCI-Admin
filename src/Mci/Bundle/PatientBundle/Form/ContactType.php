<?php

namespace Mci\Bundle\PatientBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('number','text',array(

                'attr' => array('class'=>'form-control'),
                'required'  => false
            ))
            ->add('country_code','text',array(
                'attr' => array('class'=>'form-control phone-block'),
                'required'  => false
            ))
            ->add('area_code','text',array(
                'attr' => array('class'=>'form-control phone-block'),
                'required'  => false
            ))
            ->add('extension','text',array(
                'attr' => array('class'=>'form-control phone-block'),
                'required'  => false
            ));
    }

    public function getName()
    {
        return 'mci_bundle_patientBundle_patients_contact';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mci\Bundle\PatientBundle\FormMapper\Contact'
        ));
    }
}

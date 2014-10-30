<?php

namespace Mci\Bundle\PatientBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RelationType extends AbstractType
{
    public $data;

    public function __construct($contact){
        $this->data = $contact;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('uid','text')
            ->add('nid','text')
            ->add('bin_brn','text')
            ->add('type','text')
            ->add('sur_name','text')
            ->add('given_name','text')
            ->add('name_bangla','text');
    }

    public function getName()
    {
        return 'mci_bundle_patientBundle_patients_relation';
    }
}

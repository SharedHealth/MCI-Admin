<?php

namespace Mci\Bundle\PatientBundle\Form;
use Mci\Bundle\PatientBundle\Services\MasterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RelationType extends AbstractType
{
    private $masterData;

    public function __construct( MasterData $masterData){
        $this->masterData =$masterData;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $type = $this->masterData->getAllByType('relations');
        $relations = $this->masterData->getAllByType('relational_status');

        $builder
            ->add('id', 'hidden', array(
                'attr' => array('class' => 'form-control relation-id'),
                'required'  => false
            ))
            ->add('uid', 'text', array(
                'attr' => array('class' => 'form-control relation-uid'),
                'required'  => false
            ))
            ->add('hid', 'text', array(
                'attr' => array('class' => 'form-control relation-hid'),
                'required'  => false
            ))
            ->add('nid', 'text', array(
                'attr' => array('class' => 'form-control relation-nid'),
                'required'  => false
            ))
            ->add('bin_brn', 'text', array(
                'attr' => array('class' => 'form-control relation-brn'),
                'required'  => false
            ))
            ->add('type', 'choice', array(
                'choices' => $type,
                'attr' => array('class' => 'form-control relation-type')
              )
            )
            ->add('sur_name', 'text', array(
                'attr' => array('class' => 'form-control relation-sur-name'),
                'required'  => false
            ))
            ->add('given_name', 'text', array(
                'attr' => array('class' => 'form-control relation-given-name'),
                'required'  => false
            ))
            ->add('name_bangla', 'text', array(
                'attr' => array('class' => 'form-control relation-name-bangla'),
                'required'  => false,
            ))
            ->add('marriage_id', 'text', array(
                'attr' => array('class' => 'form-control relation-marriage-id'),
                'required'  => false,
            ))
            ->add('relational_status', 'choice', array(
                'choices' => $relations,
                'empty_value' => 'N/A',
                'required'  => false,
                'attr' => array('class' => 'form-control relation-relational-status')
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mci\Bundle\PatientBundle\FormMapper\Relation'
        ));
    }


    public function getName()
    {
        return 'mci_bundle_patientBundle_patients_relation';
    }
}

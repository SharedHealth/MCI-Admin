<?php

namespace Mci\Bundle\PatientBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RelationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $type = (array)json_decode(file_get_contents('assets/json/relation.json'));
        $relations = (array)json_decode(file_get_contents('assets/json/relationalStatus.json'));

        $builder
            ->add('uid', 'text', array(
                'attr' => array('class' => 'form-control relation-uid'),
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
                'attr' => array('class' => 'form-control relation-sur_name'),
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
            ->add('relational_status', 'choice', array(
                'choices' => $relations,
                'empty_value' => '--Please select--',
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

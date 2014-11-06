<?php

namespace Mci\Bundle\PatientBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RelationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $type = (array)json_decode(file_get_contents('/assets/json/relation.json'));
        $relations = (array)json_decode(file_get_contents('/assets/json/relationalStatus.json'));

        $builder
            ->add('uid', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('nid', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('bin_brn', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('type', 'choice', array(
                'choices' => $type,
                'attr' => array('class' => 'form-control')
              )
            )
            ->add('sur_name', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('given_name', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('name_bangla', 'text', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('relational_status', 'choice', array(
                'choices' => $relations,
                'attr' => array('class' => 'form-control')
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

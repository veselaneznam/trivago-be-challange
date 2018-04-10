<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/16/16
 * Time: 1:29 PM
 */

namespace AppBundle\Form;


use AppBundle\Entity\Criteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCriteriaType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                [
                    'label' => 'Criteria Name',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Name'
                    ]
                ]
            )
            ->add(
                'alternative_name',
                'textarea',
                [
                    'label' => 'Alternative Names',
                    'required' => true
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Criteria::class
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'criteria_add_form';
    }

}
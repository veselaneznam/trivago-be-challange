<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/16/16
 * Time: 1:29 PM
 */

namespace AppBundle\Form;

use AppBundle\Entity\Negative;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddNegativeType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'negative',
                TextareaType::class,
                [
                    'label' => 'Negative',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Negative'
                    ]
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
                'data_class' => Negative::class
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'negative_add_form';
    }

}
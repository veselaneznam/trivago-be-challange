<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/16/16
 * Time: 1:29 PM
 */

namespace AppBundle\Form;

use AppBundle\Entity\Positive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPositiveType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'positive',
                TextareaType::class,
                [
                    'label' => 'Positive',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Positive'
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
                'data_class' => Positive::class
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'positive_add_form';
    }

}
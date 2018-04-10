<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/19/16
 * Time: 8:59 PM
 */

namespace AppBundle\Form;


use AppBundle\Entity\Hotel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddHotelType extends AbstractType
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
                TextareaType::class,
                [
                    'label' => 'Hotel Name',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Hotel Name'
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
                'data_class' => Hotel::class
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'hotel_add_form';
    }
}
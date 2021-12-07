<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 11/04/19
 * Time: 11:25
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerialNumberLmecoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantityCreated', IntegerType::class, [
                'label' => 'app.entity.SerialNumberLmeco.quantity_created',
                'attr' => [
                    'min' => 1
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }

}
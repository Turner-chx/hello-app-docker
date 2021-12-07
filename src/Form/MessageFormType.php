<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 02/07/19
 * Time: 11:27
 */

namespace App\Form;


use App\Entity\Messaging;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('message',TextareaType::class, [
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'app.entity.Sav.follow_my_sav.submit',
                'attr' => [
                    'class' =>'btn submit-button'
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Messaging::class,
        ]);
    }
}
<?php

namespace App\Form;

use App\Entity\StatusSetting;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('setting', EntityType::class, [
                'label' => 'app.entity.StatusSetting.name',
                'placeholder' => 'Choisir un état',
                'class' => StatusSetting::class,
            ])
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'autocomplete' => 'off'
                ],
                'label' => 'app.entity.Stats.date_start',
                'required' => false,
            ])
            ->add('dateEnd', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'autocomplete' => 'off'
                ],
                'label' => 'app.entity.Stats.date_end',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Afficher le graphique'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}

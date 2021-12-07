<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app.entity.Customer.field.name',
                'required' => true,
                'attr' => [
                    'class'=>'form-control',
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'app.entity.Customer.field.email',
                'required' => true,
                'attr' => [
                    'class'=>'form-control',
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();',
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'app.entity.Customer.field.address',
                'required' => true,
                'attr' => [
                    'class'=>'form-control',
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();',
                ],
            ])
            ->add('additionalAddress',TextareaType::class, [
                'label' => 'app.entity.Customer.field.additionalAddress',
                'required' => false,
                'attr' => [
                    'class'=>'form-control',
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();',
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'app.entity.Customer.field.postalCode',
                'required' => true,
                'attr' => [
                    'class'=>'form-control'
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'app.entity.Customer.field.city',
                'required' => true,
                'attr' => [
                    'class'=>'form-control',
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();',
                ],
            ])
            ->add('country', CountryType::class, [
                'label' => 'app.entity.Customer.field.country',
                'required' => true,
                'attr' => [
                    'class'=>'form-control',
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();',
                ],
                'preferred_choices' => ['FR']
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'app.entity.Customer.field.phoneNumber',
                'required' => true,
                'attr' => [
                    'class'=>'form-control'
                ],
            ])
            ->add('customerContact', TextType::class, [
                'label' => 'app.entity.Customer.field.customerContact',
                'required' => false,
                'attr' => [
                    'class'=>'form-control d-none',
                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();',
                ],
                'label_attr' => [
                    'class' => 'd-none'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}

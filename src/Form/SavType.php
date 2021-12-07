<?php

namespace App\Form;

use App\Entity\Sav;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SavType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('savArticles', CollectionType::class, [
                    'label' => false,
                    'entry_type' => SavArticleType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'by_reference' => false,
                    'required' => true
                ]
            )
            ->add('store', TextType::class, [
                'label' => 'app.entity.Sav.field.store',
                'required' => true
            ])
            ->add('customerPrinter', TextType::class, [
                'label' => 'app.entity.Sav.field.customer_printer',
                'required' => true
            ])
            ->add('customer', CustomerType::class, [
                'label' => false
            ])
            ->add('savFilesProof', FileType::class, [
                    'label' => 'app.entity.Sav.form.file',
                    'multiple' => true,
                    'required' => true,
                    'attr' => [
                        'lang' => 'fr'
                    ]
                ]
            )
            ->add('description', TextareaType::class, [
                'label' => 'app.entity.Sav.field.description',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sav::class,
        ]);
    }
}

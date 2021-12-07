<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\NatureSetting;
use App\Entity\SavArticle;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class SavArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('article', Select2EntityType::class, [
                'class' => Article::class,
                'remote_route' => 'ajax_autocomplete',
                'property' => ['designation', 'ean', 'reference'],
                'language' => 'fr',
                'required' => true,
                'minimum_input_length' => 2,
                'label' => 'app.entity.SavArticle.form.product',
            ])
            ->add('natureSettings', EntityType::class, [
                'class' => NatureSetting::class,
                'multiple' => true,
                'required' => true,
                'attr' => [
                    'class' => 'nature-settings'
                ],
                'label' => 'app.entity.SavArticle.form.natureSettings',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('n')
                        ->where('n.status = :true')
                        ->setParameter('true', true);
                },
            ])
            ->add('fileUnknown', FileType::class, [
                'label' => 'app.entity.SavArticle.form.fileUnknown',
                'required' => false
            ])
            ->add('unknownArticle', TextType::class, [
                'label' => 'app.entity.SavArticle.form.product',
                'required' => false
            ])
            ->add('serialNumber', TextType::class, [
                'label' => 'app.entity.SavArticle.form.serialNumber',
                'required' => false
            ])
            ->add('serialNumber2', TextType::class, [
                'label' => 'app.entity.SavArticle.form.serialNumber2',
                'required' => false
            ])
            ->add('filesProof', FileType::class, [
                    'label' => 'app.entity.Sav.form.dysfonctionning_proof',
                    'multiple' => true,
                    'required' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SavArticle::class,
        ]);
    }
}

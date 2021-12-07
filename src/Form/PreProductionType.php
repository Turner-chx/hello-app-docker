<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 05/06/19
 * Time: 14:10
 */

namespace App\Form;


use App\Entity\Arrival;
use App\Entity\Article;
use App\Entity\FeatureSubProductType;
use App\Entity\Supplier;
use App\Entity\User;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreProductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('createdAt', DateTimeType::class, [
                'label' => 'app.entity.Production.field.created_at.name',
                'disabled' => true,
                'data' => new DateTime(),
                'required' => false,
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker'
                ]
            ])
            ->add('supplier', EasyAdminAutocompleteType::class, [
                'class' => Supplier::class,
                'label' => 'app.entity.Production.field.supplier.name'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'app.entity.Production.field.user.name',
                'attr' => [
                    'class' => 'user-class'
                ]
            ])
            ->add('arrival', EasyAdminAutocompleteType::class, [
                'class' => Arrival::class,
                'label' => 'app.entity.Production.field.arrival.name',
                'required' => false
            ])
            ->add('article', EasyAdminAutocompleteType::class, [
                'class' => Article::class,
                'label' => 'app.entity.Production.field.article.name'
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'app.entity.Production.field.quantity.name',
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('features', EntityType::class, [
                'label' => 'app.entity.Production.field.feature.name',
                'multiple' => true,
                'expanded' => true,
                'class' => FeatureSubProductType::class,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
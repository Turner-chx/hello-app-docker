<?php

namespace App\Form;

use App\Entity\FeatureSubProductType;
use App\Entity\Production;
use App\Entity\SerialNumberLmeco;
use App\Repository\SerialNumberLmecoRepository;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ProductionType extends AbstractType
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serialNumber', TextType::class, [
                'label' => 'app.entity.Production.field.serial_number.name',
                'required' => true
            ])
            ->add('serialNumberLmeco', EntityType::class, [
                'label' => 'app.entity.Production.field.serial_number_lmeco.name',
                'class' => SerialNumberLmeco::class,
                'required' => false
            ])

            ->add('comment', TextareaType::class, [
                'label' => 'app.entity.Production.field.comment.name',
                'required' => false
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
            'data_class' => Production::class,
            'action' => $this->router->generate('newProduction'),
            'method' => 'post'
        ]);
    }
}

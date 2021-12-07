<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class BrandAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('brand', null, ['label' => 'app.entity.Brand.field.brand'])
            ->add('codeLama', null, ['label' => 'app.entity.Brand.field.codeLama'])
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('brand', null, [
                'label' => 'app.entity.Brand.field.brand',
                'editable' => true
            ])
            ->add('codeLama', null, [
                'label' => 'app.entity.Brand.field.codeLama',
                'editable' => true
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('brand', TextType::class, ['label' => 'app.entity.Brand.field.brand'])
            ->add('codeLama', TextType::class, ['label' => 'app.entity.Brand.field.codeLama'])
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }
}

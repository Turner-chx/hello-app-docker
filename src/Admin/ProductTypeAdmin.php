<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class ProductTypeAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('type', null, ['label' => 'app.entity.ProductType.field.type'])
            ->add('codeLama', null, ['label' => 'app.entity.ProductType.field.codeLama'])
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('type', null, [
                'label' => 'app.entity.ProductType.field.type',
                'editable' => true,
            ])
            ->add('codeLama', null, [
                'label' => 'app.entity.ProductType.field.codeLama',
                'editable' => true,
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('type', null, ['label' => 'app.entity.ProductType.field.type'])
            ->add('codeLama', null, ['label' => 'app.entity.ProductType.field.codeLama'])
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }
}

<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

final class DealerAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('email', null, ['label' => 'app.entity.Dealer.field.email'])
            ->add('address', null, ['label' => 'app.entity.Dealer.field.address'])
            ->add('additionalAddress', null, ['label' => 'app.entity.Dealer.field.additionalAddress'])
            ->add('postalCode', null, ['label' => 'app.entity.Dealer.field.postalCode'])
            ->add('city', null, ['label' => 'app.entity.Dealer.field.city'])
            ->add('createdAt', 'doctrine_orm_datetime', [
                'field_type' => DateTimePickerType::class,
                'label' => 'app.entity.Dealer.field.createdAt'
            ])
            ->add('country', 'doctrine_orm_choice', [
                    'global_search' => true,
                    'field_type' => CountryType::class,
                    'label' => 'app.entity.Dealer.field.country',
                    'preferred_choices' => ['FR']
            ])
            ->add('name', null, ['label' => 'app.entity.Dealer.field.name'])
            ->add('dealerCode', null, ['label' => 'app.entity.Dealer.field.dealerCode'])
            ->add('salesmanName', null, ['label' => 'app.entity.Dealer.field.salesmanName'])
//            ->add('status', null, ['label' => 'app.entity.Dealer.field.status'])
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('name', null, ['label' => 'app.entity.Dealer.field.name'])
            ->add('dealerCode', null, ['label' => 'app.entity.Dealer.field.dealerCode'])
            ->add('email', null, ['label' => 'app.entity.Dealer.field.email'])
            ->add('address', null, ['label' => 'app.entity.Dealer.field.address'])
            ->add('additionalAddress', null, ['label' => 'app.entity.Dealer.field.additionalAddress'])
            ->add('postalCode', null, ['label' => 'app.entity.Dealer.field.postalCode'])
            ->add('city', null, ['label' => 'app.entity.Dealer.field.city'])
            ->add('country', null, ['label' => 'app.entity.Dealer.field.country'])
            ->add('createdAt', null, ['label' => 'app.entity.Dealer.field.createdAt'])
            ->add('salesmanName', null, ['label' => 'app.entity.Dealer.field.salesmanName'])
//            ->add('status', null, ['label' => 'app.entity.Dealer.field.status'])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name', null, ['label' => 'app.entity.Dealer.field.name'])
            ->add('dealerCode', null, ['label' => 'app.entity.Dealer.field.dealerCode'])
            ->add('email', null, ['label' => 'app.entity.Dealer.field.email'])
            ->add('address', null, ['label' => 'app.entity.Dealer.field.address'])
            ->add('additionalAddress', null, ['label' => 'app.entity.Dealer.field.additionalAddress'])
            ->add('postalCode', null, ['label' => 'app.entity.Dealer.field.postalCode'])
            ->add('city', null, ['label' => 'app.entity.Dealer.field.city'])
            ->add('country', CountryType::class, ['label' => 'app.entity.Dealer.field.country', 'preferred_choices' => ['FR']])
            ->add('salesmanName', null, ['label' => 'app.entity.Dealer.field.salesmanName'])
            ->add('status', null, ['label' => 'app.entity.Dealer.field.status'])
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
    }
}

<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class NatureSettingAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('setting', null, ['label' => 'app.entity.NatureSetting.field.setting'])
            ->add('codeDivalto', null, ['label' => 'app.entity.NatureSetting.field.code_divalto'])
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('setting', null, [
                'label' => 'app.entity.NatureSetting.field.setting',
                'editable' => true
            ])
            ->add('codeDivalto', null, [
                'label' => 'app.entity.NatureSetting.field.code_divalto',
                'editable' => true
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('setting', null, ['label' => 'app.entity.NatureSetting.field.setting'])
            ->add('codeDivalto', null, ['label' => 'app.entity.NatureSetting.field.code_divalto'])
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }
}

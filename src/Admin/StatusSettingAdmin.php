<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

final class StatusSettingAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('setting', null, ['label' => 'app.entity.StatusSetting.field.setting'])
            ->add('status', null, ['label' => 'app.entity.StatusSetting.field.status'])
            ->add('byDefault', null, ['label' => 'app.entity.StatusSetting.field.by_default'])
            ->add('over', null, ['label' => 'app.entity.StatusSetting.field.over'])
            ->add('displayDivaltoReplaceButton', null, ['label' => 'app.entity.StatusSetting.field.display_divalto_replace_button'])
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('setting', null, ['label' => 'app.entity.StatusSetting.field.setting',
                'editable' => true
            ])
            ->add('status', null, ['label' => 'app.entity.StatusSetting.field.status',
                'editable' => true
            ])
            ->add('byDefault', null, [
                'label' => 'app.entity.StatusSetting.field.by_default',
                'editable' => true,
            ])
            ->add('over', null, [
                'label' => 'app.entity.StatusSetting.field.over',
                'editable' => true,
            ])
            ->add('displayDivaltoReplaceButton', null, [
                'label' => 'app.entity.StatusSetting.field.display_divalto_replace_button',
                'editable' => true,
            ])
            ->add('colorHtml', 'html', ['label' => 'app.entity.StatusSetting.field.color'])
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('app.entity.StatusSetting.name')
            ->with('app.entity.Sav.form.info', ['class' => 'col-md-2'])->end()
            ->with('app.entity.Sav.field.status_setting', ['class' => 'col-md-10'])->end()
            ->end()
        ;

        $formMapper
            ->tab('app.entity.StatusSetting.name')
            ->with('app.entity.Sav.form.info')
            ->add('setting', null, ['label' => 'app.entity.StatusSetting.field.setting'])
            ->add('color', ColorType::class, ['label' => 'app.entity.StatusSetting.field.display_divalto_replace_button'])
            ->end()
            ->with('app.entity.Sav.field.status_setting')
            ->add('status', null, ['label' => 'app.entity.StatusSetting.field.status'])
            ->add('byDefault', null, ['label' => 'app.entity.StatusSetting.field.by_default'])
            ->add('over', null, ['label' => 'app.entity.StatusSetting.field.over'])
            ->add('displayDivaltoReplaceButton', null, ['label' => 'app.entity.StatusSetting.field.display_divalto_replace_button'])
            ->end()
            ->end()
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }
}

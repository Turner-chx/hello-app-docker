<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DateTimePickerType;

final class SavHistoryAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('historyDate')
            ->add('event')
            ->add('userName')
            ->add('comment')
            ->add('statusSetting')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('historyDate')
            ->add('event')
            ->add('userName')
            ->add('comment')
            ->add('statusSetting')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('historyDate', DateTimePickerType::class, [
                'label' => 'app.entity.SavHistory.field.history_date',
                'disabled' => true
            ])
            ->add('event', null, [
                'label' => 'app.entity.SavHistory.field.event',
                'disabled' => true
            ])
            ->add('userName', null, [
                'label' => 'app.entity.SavHistory.field.user_name',
                'disabled' => true
            ])
            ->add('comment', null, [
                'label' => 'app.entity.SavHistory.field.comment',
                'disabled' => true
            ])
            ->add('statusSetting', null, [
                'label' => 'app.entity.SavHistory.field.status_setting',
                'disabled' => true
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('historyDate')
            ->add('event')
            ->add('userName')
            ->add('comment')
            ->add('statusSetting')
            ;
    }
}

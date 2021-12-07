<?php

declare(strict_types=1);

namespace App\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;

final class MessagingAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('message')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('sender')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('message')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('sender')
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
            ->add('message', CKEditorType::class, ['label' => 'app.entity.Messaging.field.new_message'])
            ->add('files', CollectionType::class, [
                'required' => false,
                'by_reference' => false,
                'label' => 'app.entity.Messaging.field.file',
                'btn_add' => 'Ajouter un fichier',
                'type_options' => [
                    // Prevents the "Delete" option from being displayed
                    'delete' => false,
                ]
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('message')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('sender')
            ;
    }
}

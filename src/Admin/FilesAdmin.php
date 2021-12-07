<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

final class FilesAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('updatedAt')
            ->add('createdAt')
            ->add('sender')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('updatedAt')
            ->add('createdAt')
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
            ->add('file', VichFileType::class, [
                'required' => false,
                'allow_delete' => false,
                'label' => 'app.entity.Sav.form.file',
                'constraints' => [
                    new File([
                        'maxSize' => '15M',
//                        'mimeTypes' => [
//                            'application/pdf',
//                            'image/gif',
//                            'image/png',
//                            'image/jpeg',
//                            'image/jpg',
//                            'application/msword',
//                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
//                            'application/vnd.ms-excel',
//                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
//                            'text/csv',
//                            'text/plain'
//                        ]
                    ])
                ]
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('updatedAt')
            ->add('createdAt')
            ->add('sender')
            ;
    }
}

<?php

declare(strict_types=1);

namespace App\Admin;

use App\Form\FilesType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ColorAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('idLama', null, ['label' => 'app.entity.Color.field.idLama'])
            ->add('color', null, ['label' => 'app.entity.Color.field.color'])
            ->add('name', null, ['label' => 'app.entity.Color.field.name'])
            ->add('isPack', null, ['label' => 'app.entity.Color.field.isPack'])
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('idLama', null, [
                'editable' => true,
                'label' => 'app.entity.Color.field.idLama'
            ])
            ->add('color', null, [
                'editable' => true,
                'label' => 'app.entity.Color.field.color'
            ])
            ->add('name', null, [
                'editable' => true,
                'label' => 'app.entity.Color.field.name'
            ])
            ->add('isPack', null, [
                'editable' => true,
                'label' => 'app.entity.Color.field.isPack'
            ])
            ->add('image', 'string', [
                'label' => 'app.entity.Color.field.image',
                'template' => 'admin\article\fields\media_article_color_thumbnail.html.twig'
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('idLama', null, ['label' => 'app.entity.Color.field.idLama'])
            ->add('color', null, ['label' => 'app.entity.Color.field.color'])
            ->add('name', null, ['label' => 'app.entity.Color.field.name'])
            ->add('isPack', null, ['label' => 'app.entity.Color.field.isPack'])
            ->add('image', FilesType::class, ['label' => 'app.entity.Color.field.image'])
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
    }
}

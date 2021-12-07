<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Dealer;
use App\Entity\Gamme;
use App\Form\FilesType;
use App\Library\Autocompleter;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

final class SourceAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name', null, ['label' => 'app.entity.Source.field.name'])
            ->add('color', null, ['label' => 'app.entity.Source.field.color'])
            ->add('gammes', null, ['label' => 'app.entity.Source.field.gamme'])
            ->add('dealer', null, ['label' => 'app.entity.Source.field.dealer'])
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('image', null, [
                'template' => 'admin/source/media_source_thumbnail.html.twig',
                'label' => 'app.entity.Source.field.image', 'editable' => false
            ])
            ->add('defaultSource', null, ['label' => 'app.entity.Source.field.default_source', 'editable' => true])
            ->add('name', null, [
                'label' => 'app.entity.Source.field.name',
                'editable' => true,
            ])
            ->add('dealer', null, ['label' => 'app.entity.Source.field.dealer'])
            ->add('gammes', null, ['label' => 'app.entity.Source.field.gamme'])
            ->add('colorHtml', 'html', ['label' => 'app.entity.Source.field.color'])
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
            ->with('app.entity.Source.form.source', ['class' => 'col-md-10'])->end()
            ->end()
        ;

        $formMapper
            ->tab('app.entity.StatusSetting.name')
            ->with('app.entity.Sav.form.info')
            ->add('image', FilesType::class, ['label' => 'app.entity.Source.field.image'])
            ->add('color', ColorType::class, ['label' => 'app.entity.Source.field.color'])
            ->add('defaultSource', null, ['label' => 'app.entity.Source.field.default_source'])
            ->end()
            ->with('app.entity.Source.form.source')
            ->add('name', null, ['label' => 'app.entity.Source.field.name'])
            ->add('dealer', ModelAutocompleteType::class, [
                'label' => 'app.entity.Source.field.dealer',
                'class' => Dealer::class,
                'property' => Autocompleter::dealerAutocomplete(),
                'required' => false
            ])
            ->add('gammes', ModelAutocompleteType::class, [
                'label' => 'app.entity.Source.field.gamme',
                'class' => Gamme::class,
                'multiple' => true,
                'property' => Autocompleter::gammeAutocomplete(),
                'minimum_input_length' => 0,
                'required' => false
            ])
            ->add('dealerEmail', null, ['label' => 'app.entity.Source.field.dealer_email'])
            ->add('emails', CollectionType::class, [
                'label' => 'app.entity.Source.field.recipients',
                'by_reference' => false,
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->end()
            ->end()
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }
}

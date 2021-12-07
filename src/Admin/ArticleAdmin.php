<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

final class ArticleAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('reference', null, ['label' => 'app.entity.Article.field.reference'])
            ->add('designation', null, ['label' => 'app.entity.Article.field.designation'])
            ->add('designationAbridged', null, ['label' => 'app.entity.Article.field.designationAbridged'])
            ->add('ean', null, ['label' => 'app.entity.Article.field.ean'])
            ->add('status', null, ['label' => 'app.entity.Article.field.status'])
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('reference', null, ['label' => 'app.entity.Article.field.reference'])
            ->add('designation', null, ['label' => 'app.entity.Article.field.designation'])
            ->add('designationAbridged', null, ['label' => 'app.entity.Article.field.designationAbridged'])
            ->add('ean', null, ['label' => 'app.entity.Article.field.ean'])
            ->add('productType.type', null, ['label' => 'app.entity.Article.field.productType'])
            ->add('brand.brand', null, ['label' => 'app.entity.Article.field.brand'])
            ->add('gamme.gamme', null, ['label' => 'app.entity.Article.field.gamme'])
            ->add('oem.oem', null, ['label' => 'app.entity.Article.field.oem'])
            ->add('color', 'string', [
                'label' => 'app.entity.Article.field.color',
                'template' => 'admin\article\fields\media_article_color_thumbnail.html.twig'
            ])
            ->add('status', null, [
                'editable' => true,
                'label' => 'app.entity.Article.field.status'
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('reference', null, ['label' => 'app.entity.Article.field.reference'])
            ->add('designation', null, ['label' => 'app.entity.Article.field.designation'])
            ->add('designationAbridged', null, ['label' => 'app.entity.Article.field.designationAbridged'])
            ->add('ean', null, ['label' => 'app.entity.Article.field.ean'])
            ->add('status', null, ['label' => 'app.entity.Article.field.status'])
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('create');
        $collection->remove('delete');
        $collection->add('importArticles');
    }

    public function configureActionButtons($action, $object = null): array
    {
        $list = parent::configureActionButtons($action, $object);

        $list['import']['template'] = 'admin/article/action/import.html.twig';

        return $list;
    }
}

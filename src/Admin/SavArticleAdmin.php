<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Article;
use App\Entity\NatureSetting;
use App\Entity\SavArticle;
use App\Library\Autocompleter;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class SavArticleAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
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
        /** @var SavArticle $savArticle */
        $savArticle = $this->getSubject();

        /** @var Article $article */
        $article = $savArticle->getArticle();

        if (null !== $article) {
            $class = 'd-none hide';
        }

        $formMapper
            ->add('article', ModelAutocompleteType::class, [
                'class' => Article::class,
                'label' => 'app.entity.Sav.field.article',
                'required' => false,
                'property' => Autocompleter::articleAutocomplete(),
                'attr' => [
                    'class' => 'col-md-8'
                ]
            ])
            ->add('natureSettings', EntityType::class, [
                'class' => NatureSetting::class,
                'label' => 'app.entity.Sav.field.natures_setting',
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'col-md-4'
                ]
            ])
            ->add('serialNumber', null, ['label' => 'app.entity.SavArticle.form.serialNumber'])
            ->add('serialNumber2', null, ['label' => 'app.entity.SavArticle.form.serialNumber2'])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ;
    }
}

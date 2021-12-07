<?php

namespace App\Form;

use App\Entity\SavArticle;
use App\Form\DataTransformer\ArticleTransformer;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ArticleAutocompleteType extends AbstractType
{
    public $articleRepository;
    public $router;

    public function __construct(ArticleRepository $articleRepository, RouterInterface $router)
    {
        $this->articleRepository = $articleRepository;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ArticleTransformer(
            $this->articleRepository,
            $options['finder_callback']
        ));

    }

    public function getParent()
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'Aucun article trouvé',
            'finder_callback' => function(ArticleRepository $articleRepository, string $designation) {
            return $articleRepository->findOneBy(['designation' => $designation]);
            },
            'attr' => [
                'class' => 'js-article-autocomplete',
                'data-autocomplete-url' => $this->router->generate('autocomplete_article')
            ]
        ]);
    }
}

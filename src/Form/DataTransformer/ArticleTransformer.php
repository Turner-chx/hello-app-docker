<?php

namespace App\Form\DataTransformer;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArticleTransformer implements DataTransformerInterface {
    private $manager;

    public function __construct(ArticleRepository $manager) {
        $this->manager = $manager;
    }

    public function transform($article) {
        if (null === $article) {
            return '';
        }
        return $article->getId();
    }

    public function reverseTransform($articleId) {
        if (!$articleId) {
            return;
        }

        $article = $this->manager->find($articleId)
        ;

        if (null === $article) {
            throw new TransformationFailedException(sprintf(
                'Article non trouvé',
                $articleId
            ));
        }

        return $article;
    }
}
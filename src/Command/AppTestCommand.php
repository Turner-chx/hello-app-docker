<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 10/04/19
 * Time: 15:19
 */

namespace App\Command;


use App\Entity\Article;
use App\Entity\Guarantee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppTestCommand extends Command
{

    protected static $defaultName = 'app:test';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Commande de test pour le gros débile Alexandre Lagoutte');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;

        $articles = $manager->getRepository(Article::class)->findAll();

        $guarantee = $manager->getRepository(Guarantee::class)
            ->findOneBy([
                'byDefault' => true
            ]);

        foreach ($articles as $article) {
            $article->setGuarantee($guarantee);

            $manager->persist($article);
        }

        $manager->flush();
    }
}
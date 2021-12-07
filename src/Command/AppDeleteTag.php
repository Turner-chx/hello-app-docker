<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 23/10/19
 * Time: 10:01
 */

namespace App\Command;


use App\Entity\Sav;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppDeleteTag extends Command
{

    protected static $defaultName = 'app:delete:tags';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Supprime les balises HTML des commentaires SAV');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->em;

        $savs = $em->getRepository(Sav::class)->findAll();

        /** @var Sav $sav */
        foreach ($savs as $sav) {

            $comment = $sav->getComment();

            if (null !== $comment) {
                $newComment = strip_tags(html_entity_decode(htmlspecialchars_decode($comment), ENT_QUOTES));
                $sav->setComment($newComment);
                $this->em->persist($sav);
                $this->em->flush();

            }

        }
    }
}
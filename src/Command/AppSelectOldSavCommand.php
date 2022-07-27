<?php

namespace App\Command;

use App\Handler\SelectOldSavHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppSelectOldSavCommand extends Command
{
    protected static $defaultName = 'app:selectOldSav';

    protected $em;
    private $selectOldSavHandler;

    public function __construct(EntityManagerInterface $em,SelectOldSavHandler $selectOldSavHandler )
    {
        $this->em = $em;
        $this->selectOldSavHandler = $selectOldSavHandler;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Commande pour selectioner les sav de plus de 30 jour');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->selectOldSavHandler->selectOldSav();
    }
}
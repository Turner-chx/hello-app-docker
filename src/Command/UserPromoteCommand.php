<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserPromoteCommand extends Command
{
    protected static $defaultName = 'app:user:promote';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Promote user role')
            ->addArgument('email', InputArgument::OPTIONAL, 'User email to promote')
            ->addArgument('role', InputArgument::OPTIONAL, 'Role to add to user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        if (!$email) {
            $email = $io->ask('Which user email ?');
        }

        if (!$role) {
            $role = $io->ask('Which user role ?');
        }

        /** @var User $user */
        $user = $this->em->getRepository(User::class)
            ->findOneBy([
                'email' => $email
            ]);

        if (!$user) {
            $io->error(sprintf('User %s does not exist', $email));
            return;
        }

        $user->addRole($role);
        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Role %s successfully added to user %s', $role, $email));
    }
}

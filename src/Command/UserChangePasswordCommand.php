<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserChangePasswordCommand extends Command
{
    protected static $defaultName = 'app:user:change-password';
    private $em;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoder $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->setDescription('Change user password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Which user email ?');

        /** @var User $user */
        $user = $this->em->getRepository(User::class)
            ->findOneBy([
                'email' => $email
            ]);

        if (!$user) {
            $io->error(sprintf('User %s does not exist', $email));
            return;
        }

        $newPassword = $io->ask('Type the new password');
        $newPasswordRepeated = $io->ask('Confirm new password');

        if ($newPassword !== $newPasswordRepeated) {
            $io->error(sprintf('Passwords are not the same'));
            return;
        }

        $password = $this->passwordEncoder->encodePassword($user, $newPassword);

        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Password successfully changed for user %s', $email));
    }
}

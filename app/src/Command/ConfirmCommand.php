<?php

namespace App\Command;

use App\ReadModel\User\UserFetcher;
use App\Model\User\UseCase\SignUp\Confirm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ConfirmCommand
 * @package App\Command
 * @property UserFetcher $users
 * @property Confirm\Manual\Handler $handler
 */
class ConfirmCommand extends Command
{

    private UserFetcher $users;
    private Confirm\Manual\Handler $handler;

    public function __construct(UserFetcher $users,Confirm\Manual\Handler $handler, string $name = null)
    {
        parent::__construct($name);
        $this->users = $users;
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->setName('user:confirm')
            ->setDescription('Confirm signed up user');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $io->ask('Email: ');

        if (!$user = $this->users->findByEmail($email)) {
            throw new LogicException('User is not found.');
        }

        $command=new Confirm\Manual\Command($user->id);
        $this->handler->handle($command);

        $io->success('Done!');
    }
}

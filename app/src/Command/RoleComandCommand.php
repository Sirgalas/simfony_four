<?php

namespace App\Command;

use App\Model\User\Entity\User\Role as RoleValue;
use App\Model\User\UseCase\Role;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Console\Exception\LogicException;

/**
 * Class RoleComandCommand
 * @package App\Command
 * @property  UserFetcher $users
 * @property  ValidatorInterface $validator
 * @property  Role\Handler $handler
 */
class RoleComandCommand extends Command
{

    private UserFetcher $users;
    private ValidatorInterface $validator;
    private Role\Handler $handler;

    public function __construct(UserFetcher $users, ValidatorInterface $validator, Role\Handler $handler, string $name = null)
    {
        parent::__construct($name);
        $this->users = $users;
        $this->validator = $validator;
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->setName('user:role')
            ->setDescription('Change user role');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email=$io->ask('Email: ');
        if (!$user = $this->users->findByEmail($email)) {
            throw new LogicException('User is not found.');
        }
        $command = new Role\Command($user->id);
        $roles = [RoleValue::USER, RoleValue::ADMIN];
        $command->role = $io->choice('Role: ', $roles, 0);

        $violations = $this->validator->validate($command);
        if ($violations->count()) {
            foreach ($violations as $violation) {
                $io->error( $violation->getPropertyPath() . ': ' . $violation->getMessage());
            }
            return;
        }

        $this->handler->handle($command);

        $io->success('Done!');
    }
}

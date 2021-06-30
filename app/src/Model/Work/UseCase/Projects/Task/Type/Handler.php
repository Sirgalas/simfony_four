<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Type;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Type;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\MemberRepository;

class Handler
{
    private $members;
    private $flusher;
    private $tasks;

    public function __construct(MemberRepository $members, TaskRepository $tasks, Flusher $flusher)
    {
        $this->members = $members;
        $this->flusher = $flusher;
        $this->tasks = $tasks;
    }

    public function handle(Command $command): void
    {
        $actor = $this->members->get(new MemberId($command->actor));
        $task = $this->tasks->get(new Id($command->id));

        $task->changeType($actor, new \DateTimeImmutable(), new Type($command->type));

        $this->flusher->flush($task);
    }
}
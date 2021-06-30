<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\ChildOf;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\MemberRepository;

class Handler
{
    private $members;
    private TaskRepository $tasks;
    private Flusher $flusher;

    public function __construct(MemberRepository $members, TaskRepository $tasks, Flusher $flusher)
    {
        $this->tasks = $tasks;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $actor = $this->members->get(new MemberId($command->actor));
        $task = $this->tasks->get(new Id($command->id));

        if($command->parent){
            $parent = $this->tasks->get(new Id($command->parent));
            $task->setChildOf($actor, new \DateTimeImmutable(), $parent);
        } else {
            $task->setRoot();
        }
        $this->flusher->flush();
    }
}
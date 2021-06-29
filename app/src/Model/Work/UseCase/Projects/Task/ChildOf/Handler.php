<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\ChildOf;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\Model\Work\Entity\Projects\Task\Id;

class Handler
{
    private TaskRepository $tasks;
    private Flusher $flusher;

    public function __construct(TaskRepository $tasks, Flusher $flusher)
    {
        $this->tasks = $tasks;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $task = $this->tasks->get(new Id($command->id));

        if($command->parent){
            $parent = $this->tasks->get(new Id($command->parent));
            $task->setChildOf($parent);
        } else {
            $task->setRoot();
        }
        $this->flusher->flush();
    }
}
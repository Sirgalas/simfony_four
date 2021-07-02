<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Id;

class TaskPriorityChanged
{
    private MemberId $actorId;
    private Id $taskId;
    private int $priority;

    public function __construct(MemberId $actorId, Id $taskId, int $priority)
    {
        $this->actorId = $actorId;
        $this->taskId = $taskId;
        $this->priority = $priority;
    }
}
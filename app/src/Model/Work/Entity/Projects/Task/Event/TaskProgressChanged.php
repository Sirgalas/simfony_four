<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Id;

class TaskProgressChanged
{
    private MemberId $actorId;
    private Id $taskId;
    private int $progress;

    public function __construct(MemberId $actorId, Id $taskId, int $progress)
    {
        $this->actorId = $actorId;
        $this->taskId = $taskId;
        $this->progress = $progress;
    }
}
<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Files\Id as FileId;
use App\Model\Work\Entity\Projects\Task\Files\Info;
use App\Model\Work\Entity\Projects\Task\Id;

class TaskFileAdded
{
    private MemberId $actorId;
    private Id $taskId;
    private FileId $fileId;
    private Info $info;

    public function __construct(MemberId $actorId, Id $taskId, FileId $fileId, Info $info)
    {
        $this->actorId = $actorId;
        $this->taskId = $taskId;
        $this->fileId = $fileId;
        $this->info = $info;
    }
}
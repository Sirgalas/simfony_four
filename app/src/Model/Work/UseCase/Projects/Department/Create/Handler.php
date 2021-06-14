<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Department\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Department\Id;
use App\Model\Work\Entity\Projects\Project\Id as ProjectId;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;

class Handler
{
    private ProjectRepository $projects;
    private Flusher $flusher;

    public function __construct(ProjectRepository $projects, Flusher $flusher)
    {
        $this->projects = $projects;
        $this->flusher = $flusher;
    }

    public function handle(Command $command):void
    {
        $project = $this->projects->get(new ProjectId($command->project));

        $project->addDepartment(
            Id::next(),
            $command->name
        );

        $this->flusher->flush();
    }
}
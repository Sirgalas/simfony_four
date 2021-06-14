<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Role\RoleRepository;
use App\Model\Work\Entity\Projects\Role\Id;

class Handler
{
    private RoleRepository $roles;
    private Flusher $flusher;
    private ProjectRepository $projects;

    public function __construct(RoleRepository $roles, ProjectRepository $projects, Flusher $flusher)
    {
        $this->roles = $roles;
        $this->flusher = $flusher;
        $this->projects = $projects;
    }

    public function handle(Command $command):void
    {
        $role = $this->roles->get(new Id($command->id));

        if ($this->projects->hasMembersWithRole($role->getId())) {
            throw new \DomainException('Role contains members.');
        }

        $this->roles->remove($role);

        $this->flusher->flush();
    }
}
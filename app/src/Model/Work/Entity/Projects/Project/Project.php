<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="work_projects_projects")
 */
class Project
{
    /**
     * @var Id
     * @ORM\Column (type="work_projects_project_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @var string
     * @ORM\Column (type="string")
     */
    private string $name;

    /**
     * @var int
     * @ORM\Column (type="integer")
     */
    private int $sort;

    /**
     * @var Status
     * @ORM\Column (type="work_projects_project_status", length=16)
     */
    private $status;

    public function __construct(Id $id, string $name, int $sort)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setSort($sort);
        $this->status=Status::active();
    }

    public function edit(string  $name,int $sort):void
    {
        $this->name=$name;
        $this->sort = $sort;
    }

    public function archive(): void
    {
        if ($this->isArchived()) {
            throw new \DomainException('Project is already archived.');
        }
        $this->status = Status::archived();
    }

    public function reinstate(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('Project is already active.');
        }
        $this->status = Status::active();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function setId(Id $id)
    {
        $this->id=$id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name=$name;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort)
    {
        $this->sort=$sort;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status)
    {
        $this->status=$status;
    }
}
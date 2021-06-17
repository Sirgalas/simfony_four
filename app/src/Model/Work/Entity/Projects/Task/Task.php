<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Project;
use Webmozart\Assert\Assert;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table(name="work_projects_tasks", indexes={
 *     @ORM\Index(columns={"date"})
 * })
 */
class Task
{
    /**
     * @var Id
     * @ORM\Column(type="work_projects_task_id")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="work_projects_tasks_seq", initialValue=1)
     * @ORM\Id
     */
    private $id;
    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Projects\Project\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    private $project;
    /**
     * @var Member
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Members\Member\Member")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private $author;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;
    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $planDate;
    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $startDate;
    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $endDate;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;
    /**
     * @var Type
     * @ORM\Column(type="work_projects_task_type", length=16)
     */
    private $type;
    /**
     * @ORM\Column(type="smallint")
     */
    private $progress;
    /**
     * @ORM\Column(type="smallint")
     */
    private $priority;
    /**
     * @var Task|null
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $parent;
    /**
     * @var Status
     * @ORM\Column(type="work_projects_task_status", length=16)
     */
    private $status;
    /**
     * @var Member[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Model\Work\Entity\Members\Member\Member")
     * @ORM\JoinTable(name="work_projects_tasks_executors",
     *      joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="member_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name.first" = "ASC"})
     */
    private $executors;

    public function __construct(
        Id $id,
        Project $project,
        Member $author,
        \DateTimeImmutable $date,
        Type $type,
        int $priority,
        string $name,
        ?string $content
    ){
        $this->setId($id);
        $this->setProject($project);
        $this->setAuthor($author);
        $this->setDate($date);
        $this->setName($name);
        $this->setContent($content);
        $this->setProgress(0);
        $this->setType($type);
        $this->setPriority($priority);
        $this->setStatus(Status::new());
        $this->executors = new ArrayCollection();
    }

    public function edit(string $name, ?string $content): void
    {
        $this->setName($name);
        $this->setContent($content);
    }

    public function move(Project $project): void
    {
        if ($project === $this->project) {
            throw new \DomainException('Project is already same.');
        }
        $this->project = $project;
    }

    public function start(\DateTimeImmutable $date): void
    {
        if (!$this->isNew()) {
            throw new \DomainException('Task is already started.');
        }
        if (!$this->executors->count()) {
            throw new \DomainException('Task does not contain executors.');
        }
        $this->changeStatus(Status::working(), $date);
    }

    public function changeType(Type $type): void
    {
        if ($this->type->isEqual($type)) {
            throw new \DomainException('Type is already same.');
        }
        $this->type = $type;
    }

    public function changeStatus(Status $status, \DateTimeImmutable $date): void
    {
        if ($this->status->isEqual($status)) {
            throw new \DomainException('Status is already same.');
        }
        $this->status = $status;
        if ($status->isDone() && $this->progress !== 100) {
            $this->changeProgress(100);
        }
        if (!$status->isNew() && !$this->startDate) {
            $this->setStartDate($date);
        }
        if ($status->isDone()) {
            if ($this->progress !== 100) {
                $this->changeProgress(100);
            }
            $this->setEndDate($date);
        } else {
            $this->setEndDate(null);
        }
    }

    public function changeProgress(int $progress): void
    {
        Assert::range($progress, 0, 100);
        if ($progress === $this->progress) {
            throw new \DomainException('Progress is already same.');
        }
        $this->progress = $progress;
    }

    public function changePriority(int $priority): void
    {
        Assert::range($priority, 1, 4);
        if ($priority === $this->priority) {
            throw new \DomainException('Priority is already same.');
        }
        $this->priority = $priority;
    }

    public function hasExecutor(MemberId $id): bool
    {
        foreach ($this->executors as $executor) {
            if ($executor->getId()->isEqual($id)) {
                return true;
            }
        }
        return false;
    }

    public function assignExecutor(Member $executor): void
    {
        if ($this->executors->contains($executor)) {
            throw new \DomainException('Executor is already assigned.');
        }
        $this->executors->add($executor);
    }

    public function revokeExecutor(MemberId $id): void
    {
        foreach ($this->executors as $current) {
            if ($current->getId()->isEqual($id)) {
                $this->executors->removeElement($current);
                return;
            }
        }
        throw new \DomainException('Executor is not assigned.');
    }

    public function isNew(): bool
    {
        return $this->status->isNew();
    }

    public function isWorking(): bool
    {
        return $this->status->isWorking();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function setId(Id $id):void
    {
        $this->id = $id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        return $this->project = $project;
    }

    public function getAuthor(): Member
    {
        return $this->author;
    }

    public function setAuthor(Member $member):void
    {
        $this->author = $member;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date)
    {
        $this->date=$date;
    }

    public function getPlanDate(): ?\DateTimeImmutable
    {
        return $this->planDate;
    }

    public function setPlan(?\DateTimeImmutable $date): void
    {
        $this->planDate = $date;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name=$name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content=$content;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): void
    {
        $this->type=$type;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function setProgress(int $progress)
    {
        $this->progress=$progress;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority=$priority;
    }

    public function getParent(): ?Task
    {
        return $this->parent;
    }

    public function setChildOf(?Task $parent): void
    {
        if ($parent) {
            $current = $parent;
            do {
                if ($current === $this) {
                    throw new \DomainException('Cyclomatic children.');
                }
            }
            while ($current && $current = $current->getParent());
        }

        $this->parent = $parent;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status)
    {
        $this->status=$status;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $date): void
    {
        $this->startDate = $date;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $date = null): void
    {
        $this->endDate = $date;
    }

    /**
     * @return Member[]
     */
    public function getExecutors(): array
    {
        return $this->executors->toArray();
    }

}
<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use Doctrine\ORM\EntityManagerInterface;
use App\Exceptions\EntityNotFoundException;

class TaskRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo=$em->getRepository(Task::class);
        $this->connection = $em->getConnection();
    }

    public function get(Id $id): Task
    {
        /** @var Task $task */
        if (!$task = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Task is not found.');
        }
        return $task;
    }

    public function add(Task $task): void
    {
        $this->em->persist($task);
    }

    public function remove(Task $task): void
    {
        $this->em->remove($task);
    }

    public function nextId(): Id
    {
        return new Id((int)$this->connection->executeQuery('SELECT nextval(\'work_projects_tasks_seq\')'));
    }
}
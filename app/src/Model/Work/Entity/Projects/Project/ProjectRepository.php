<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use App\Exceptions\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Work\Entity\Projects\Role\Id as RoleId;

class ProjectRepository
{
    private EntityManagerInterface $em;
    private \Doctrine\Persistence\ObjectRepository $repo;

    /**
    * @var \Doctrine\ORM\EntityRepository
    */

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Project::class);
    }

    public function hasMembersWithRole(RoleId $id): bool
    {
        return $this->repo->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->innerJoin('p.memberships', 'ms')
                ->innerJoin('ms.roles', 'r')
                ->andWhere('r.id = :role')
                ->setParameter(':role', $id->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }



    public function get(Id $id): Project
    {
        /** @var Project $project */
        if (!$project = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Project is not found.');
        }
        return $project;
    }

    public function add(Project $project): void
    {
        $this->em->persist($project);
    }

    public function remove(Project $project): void
    {
        $this->em->remove($project);
    }
}
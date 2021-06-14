<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project;

use App\ReadModel\Fetcher;
use Doctrine\DBAL\Connection;

class DepartmentFetcher extends Fetcher
{

    public function listOfProject(string $project): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('work_projects_project_departments')
            ->andWhere('project_id = :project')
            ->setParameter(':project', $project)
            ->orderBy('name');

        $stmt = $this->getStatement($qb);

        return $stmt->fetchAllAssociative();
    }
}
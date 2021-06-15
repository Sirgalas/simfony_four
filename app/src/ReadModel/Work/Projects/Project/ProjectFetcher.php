<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project;

use App\ReadModel\Fetcher;
use App\ReadModel\Work\Projects\Project\Filter\Filter;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ProjectFetcher extends Fetcher
{
    public function getMaxSort(): int
    {
        return (int)$this->connection->createQueryBuilder()
            ->select('MAX(p.sort) AS m')
            ->from('work_projects_projects', 'p')
            ->execute()
            ->fetchOne();
    }

    /**
     * @param Filter $filter
     * @param int $page
     * @param int $size
     * @param string $sort
     * @param string $direction
     * @return PaginationInterface
     */
    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'p.id',
                'p.name',
                'p.status'
            )
            ->from('work_projects_projects', 'p');

        if ($filter->member) {
            $qb->andWhere('EXISTS (
                SELECT ms.member_id FROM work_projects_project_memberships ms WHERE ms.project_id = p.id AND ms.member_id = :member
            )');
            $qb->setParameter(':member', $filter->member);
        }

        if ($filter->name) {
            $qb->andWhere($qb->expr()->like('p.name', ':name'));
            $qb->setParameter(':name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->status) {
            $qb->andWhere('p.status = :status');
            $qb->setParameter(':status', $filter->status);
        }

        if (!\in_array($sort, ['name', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}
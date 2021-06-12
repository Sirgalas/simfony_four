<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Members\Group;

use Doctrine\DBAL\Connection;
use App\ReadModel\Fetcher;

class GroupFetcher extends Fetcher
{

    public function all(): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'g.id',
                'g.name'
            )
            ->from('work_members_groups', 'g')
            ->orderBy('name');

        $stmt=$this->getStatement($qb);

        return $stmt->fetchAllAssociative();
    }

    public function assoc(): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('work_members_groups')
            ->orderBy('name');

        $stmt=$this->getStatement($qb);

        return array_column($stmt->fetchAllAssociative(), 'name', 'id');
    }
}
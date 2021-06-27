<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Task;
use App\ReadModel\Comment\CommentRow;
use Doctrine\DBAL\Connection;
use App\ReadModel\Fetcher;

class CommentFetcher extends Fetcher
{
    public function allForTask(int $id): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'c.id',
                'c.date',
                'm.id AS author_id',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) AS author_name',
                'm.email AS author_email',
                'c.text'
            )
            ->from('comment_comments', 'c')
            ->innerJoin('c', 'work_members_members', 'm', 'c.author_id = m.id')
            ->andWhere('c.entity_type = :entity_type AND c.entity_id = :entity_id')
            ->setParameter(':entity_type', Task::class)
            ->setParameter(':entity_id', $id)
            ->orderBy('c.date');

        $stmt = $this->getStatement($qb);
        $commentRow = [];
        foreach ($stmt->fetchAllAssociative() as $row){
            $commentRow[] = new CommentRow($row);
        }
        return $commentRow;
    }
}
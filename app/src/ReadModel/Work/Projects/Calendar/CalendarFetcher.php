<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Calendar;

use App\ReadModel\Fetcher;
use App\ReadModel\Work\Projects\Calendar\Query\Query;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Types\Types;
use Doctrine\DBAL\Types\Type;

class CalendarFetcher extends Fetcher
{

    public function byMonth(Query $query): Result
    {
        $month = new \DateTimeImmutable($query->year . '-' . $query->month . '-01');
        $start = self::calcFirstDayOfWeek($month)->setTime(0, 0);
        $end = $start->modify('+34 days')->setTime(23, 59, 59);

        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select(
                't.id',
                't.name',
                'p.id AS project_id',
                'p.name AS project_name',
                'to_char(t.date, \'YYYY-MM-DD\') AS date',
                't.plan_date',
                't.start_date',
                't.end_date'
            )
            ->from('work_projects_tasks', 't')
            ->leftJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->andWhere($qb->expr()->or(
                't.date BETWEEN :start AND :end',
                't.plan_date BETWEEN :start AND :end',
                't.start_date BETWEEN :start AND :end',
                't.end_date BETWEEN :start AND :end'
            ))
            ->setParameter(':start', $start, Types::DATETIME_MUTABLE)
            ->setParameter(':end', $end, Types::DATETIME_MUTABLE)
            ->orderBy('date');

        if ($query->member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 't.project_id = ms.project_id');
            $qb->andWhere('ms.member_id = :member');
            $qb->setParameter(':member', $query->member);
        }

        if ($query->project) {
            $qb->andWhere('t.project_id = :project');
            $qb->setParameter(':project', $query->project);
        }

        $stmt = $this->getStatement($qb);

        return new Result($stmt->fetchAllAssociative(), $start, $end, $month);
    }


    public function byWeek(\DateTimeImmutable $date, ?string $member): Result
    {
        $start = self::calcFirstDayOfWeek($date)->setTime(0, 0);
        $end = $start->modify('+6 days')->setTime(23, 59, 59);

        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select(
                't.id',
                't.name',
                'p.id AS project_id',
                'p.name AS project_name',
                'to_char(t.date, \'YYYY-MM-DD\') AS date',
                't.plan_date',
                't.start_date',
                't.end_date'
            )
            ->from('work_projects_tasks', 't')
            ->leftJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->andWhere($qb->expr()->or(
                't.date BETWEEN :start AND :end',
                't.plan_date BETWEEN :start AND :end',
                't.start_date BETWEEN :start AND :end',
                't.end_date BETWEEN :start AND :end'
            ))
            ->setParameter(':start', $start, Types::DATETIME_MUTABLE)
            ->setParameter(':end', $end, Types::DATETIME_MUTABLE)
            ->orderBy('date');

        if ($member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 't.project_id = ms.project_id');
            $qb->andWhere('ms.member_id = :member');
            $qb->setParameter(':member', $member);
        }

        $stmt = $this->getStatement($qb);

        return new Result($stmt->fetchAllAssociative(), $start, $end, $date);
    }

    private static function calcFirstDayOfWeek(\DateTimeImmutable $date)
    {
        if ($date->format('w') === '0') {
            return $date->modify('-6 days');
        }
        return $date->modify('-' . ($date->format('w') - 1) . ' days');
    }
}
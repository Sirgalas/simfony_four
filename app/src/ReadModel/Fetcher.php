<?php


namespace App\ReadModel;

use App\Model\User\Entity\User\User;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\ForwardCompatibility\DriverStatement;
use App\Exceptions\LogicException;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class Fetcher
{

    /**
     * @var Connection
     */
    protected Connection $connection;
    protected PaginatorInterface $paginator;
    protected $repository;




    public function __construct(Connection $connection, EntityManagerInterface $em,PaginatorInterface $paginator) {
        $this->connection = $connection;
        $this->paginator = $paginator;
        $this->repository = $em->getRepository(User::class);
    }

    final public function getStatement(QueryBuilder $queryBuilder): DriverStatement
    {
        $stmt = $queryBuilder->execute();
        if ($stmt instanceof DriverStatement) {
            return $stmt;
        }

        throw new LogicException('this method works only with the select operator');
    }

    final public function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }
}

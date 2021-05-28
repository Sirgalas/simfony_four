<?php


namespace App\ReadModel;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\ForwardCompatibility\DriverStatement;
use App\Exceptions\LogicException;

use Doctrine\DBAL\Connection;

class Fetcher
{

    /**
     * @var Connection
     */
    protected Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
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

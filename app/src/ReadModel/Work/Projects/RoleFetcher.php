<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Projects;

use App\Model\Work\Entity\Projects\Role\Role;
use App\ReadModel\Fetcher;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class RoleFetcher extends Fetcher
{
    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        parent::__construct($connection, $em, $paginator);
        $this->repository=$this->repository = $em->getRepository(Role::class);
    }

    public function get(string $id){
        return $this->repository->find($id);
    }

    public function all(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'r.id',
                'r.name',
                'r.permissions',
                '(SELECT COUNT(*) FROM work_projects_project_membership_roles m WHERE m.role_id = r.id) AS memberships_count'
            )
            ->from('work_projects_roles', 'r')
            ->orderBy('name')
            ->execute();

        return array_map(static function (array $role) {
            return array_replace($role, [
                'permissions' => json_decode($role['permissions'], true)
            ]);
        }, $stmt->fetchAllAssociative());
    }

    public function allList(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('work_projects_roles')
            ->orderBy('name')
            ->execute();

        return array_column($stmt->fetchAllAssociative(), 'name', 'id');
    }
}
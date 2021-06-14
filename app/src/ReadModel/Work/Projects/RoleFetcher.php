<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Projects;

use App\ReadModel\Fetcher;

class RoleFetcher extends Fetcher
{
    public function all(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'r.id',
                'r.name',
                'r.permissions'
            )
            ->from('work_projects_roles', 'r')
            ->orderBy('name')
            ->execute();

        return array_map(static function (array $role) {
            return array_replace($role, [
                'permissions' => json_decode($role['permissions'], true)
            ]);
        }, $stmt->fetchAll(FetchMode::ASSOCIATIVE));
    }
}
<?php


namespace App\ReadModel\User;


use App\ReadModel\AbstractCommand;
use App\ReadModel\Fetcher;
use Doctrine\DBAL\Connection;

class UserFetcher extends Fetcher
{

    public function existsByResetToken(string $token):bool
    {
        return $this->getQueryBuilder()
            ->select('COUNT(*)')
            ->from('users')
            ->where('reset_token_token=:token')
            ->setParameter(':token',$token)
            ->execute()->fetchOne()>0;
    }

    public function findForAuthByEmail(string $email):? AuthView
    {
        $qb=$this->getQueryBuilder()
            ->select('id','email','password_hash','role','status')
            ->from('user_users')
            ->where('email=:email')
            ->setParameter(':email',$email);
        $stmt = $this->getStatement($qb);
        $row = $stmt->fetchAssociative();
        if (false !== $row) {
            return new AuthView($row);
        }

        return null;
    }

    public function findForAuthByNetwork(string $network, string $identity): ?AuthView
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'u.id',
                'u.email',
                'u.password_hash',
                'u.role',
                'u.status'
            )
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->where('n.network = :network AND n.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity);

        $stmt = $this->getStatement($qb);
        $row = $stmt->fetchAssociative();
        if (false !== $row) {
            return new AuthView($row);
        }

        return null;
    }

    public function findByEmail(string $email): ?ShortView
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email);

        $stmt = $this->getStatement($qb);
        $row = $stmt->fetchAssociative();
        if (false !== $row) {
            return new ShortView($row);
        }

        return null;
    }
    public function findDetail(string $id): ?DetailView
    {

        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'created_at',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('id = :id')
            ->setParameter(':id', $id);

        $stmt = $this->getStatement($qb);
        $row = $stmt->fetchAssociative();

        /** @var DetailView $view */
        if (false !== $row) {
            return new DetailView($row);
        }

        $qb = $this->connection->createQueryBuilder()
            ->select('network', 'identity')
            ->from('user_user_networks')
            ->where('user_id = :id')
            ->setParameter(':id', $id);

        $stmt = $this->getStatement($qb);
        $row = $stmt->fetchAssociative();

        /** @var DetailView $view */
        if (false !== $row) {
            return new DetailView($row);
        }

        return $view;
    }

    public function findBySignUpConfirmToken(string $token): ?ShortView
    {
        $qb=$this->connection->createQueryBuilder()
            ->select('id','email','role','status')
            ->from('user_users')
            ->where('confirm_token = :token')
            ->setParameter(':token',$token);
        $stmt=$this->getStatement($qb);
        $row=$stmt->fetchAssociative();
        if (false !== $row) {
            return new ShortView($row);
        }

        return null;
    }
}

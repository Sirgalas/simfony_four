<?php


namespace App\ReadModel\User;


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

    public function findForAuth(string $email):? AuthView
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
}

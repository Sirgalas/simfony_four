<?php


namespace App\Security;


use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\ReadModel\User\AuthView;


class UserProvider implements UserProviderInterface
{

    /**
     * @var UserFetcher
     */
    private $users;

    public function __construct(UserFetcher $users){
        $this->users = $users;
    }
    /**
     * @inheritDoc
     */
    public function loadUserByUsername(string $username)
    {
        $user = $this->loadUser($username);
        return self::identityByUser($user);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $identity):UserInterface
    {
        if (!$identity instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . \get_class($identity));
        }

        $user = $this->loadUser($identity->getUsername());
        return self::identityByUser($user);
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class)
    {
        return $class === UserIdentity::class;
    }

    private function loadUser($username): AuthView
    {
        if (!$user = $this->users->findForAuth($username)) {
            throw new UsernameNotFoundException('');
        }
        return $user;
    }

    private static function identityByUser(AuthView $user): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->email,
            $user->password_hash,
            $user->role,
            $user->status
        );
    }
}

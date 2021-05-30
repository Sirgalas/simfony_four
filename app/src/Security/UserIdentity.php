<?php

namespace App\Security;

use App\Model\User\Entity\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class UserIdentity implements UserInterface, EquatableInterface
{

    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $role;

    private string $status;


    public function __construct(
        string $id,
        string $username,
        string $password,
        string $role,
        string $status
    )
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->status === User::STATUS_ACTIVE;
    }

    /**
     * @return string
     */
    public function getRoles():array
    {
        return [$this->role];
    }

    /**
     * @return string
     */
    public function getPassword():string
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getSalt():?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername():string
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function isEqualTo(UserInterface $user):bool
    {
        if(!$user instanceof self){
            return false;
        }

        return
            $this->id===$user->id &&
            $this->username === $user->username &&
            $this->role === $user->role &&
            $this->password === $user->password &&
            $this->status === $user->status;
    }
}

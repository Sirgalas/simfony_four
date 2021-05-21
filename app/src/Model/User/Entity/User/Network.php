<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Ramsey\Uuid\Uuid;

class Network
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $network;

    /**
     * @var string
     */
    private $identity;

    public function __construct(User $user, string $network, string $identity)
    {
        $this->id=Uuid::uuid4()->toString();
        $this->setUser($user);
        $this->setNetwork($network);
        $this->setIdentity($identity);
    }

    public function isForNetwork(string $network): bool
    {
        return $this->network === $network;
    }

    public function getUser():User
    {
        return $this->user;
    }

    public function setUser(User $user):void
    {
        $this->user=$user;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function setNetwork(string $network):void
    {
        $this->network=$network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function setIdentity(string $identity)
    {
        $this->identity=$identity;
    }

}

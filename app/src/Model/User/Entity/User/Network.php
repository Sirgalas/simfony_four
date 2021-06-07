<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_networks",
 *      uniqueConstraints={
 *      @ORM\UniqueConstraint(columns={"network", "identity"})
 *    })
 */
class Network
{
    /**
     * @var string
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    private $id;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="networks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $network;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
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

    public function isFor(string $network, string $identity): bool
    {
        return $this->network === $network && $this->identity === $identity;
    }
}

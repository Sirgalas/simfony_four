<?php

namespace App\Model\User\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_users", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"email"}),
 *     @ORM\UniqueConstraint(columns={"reset_token_token"})
 * })
 */
class User
{
    private const STATUS_WAIT='wait';
    public const STATUS_ACTIVE='active';
    /**
     * @var Id
     * @ORM\Column (type="user_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var Email|null
     * @ORM\Column(type="user_email", nullable=true)
     */
    private $email;

    /**
     * @var string|null
     * @ORM\Column (type="string", name="password_hash", nullable=true)
     */
    private $passwordHash;

    /**
     * @var string|null
     * @ORM\Column (type="string", name="confirm_token", nullable=true)
     */
    private $confirmToken;

    /**
     * @var ResetToken|null
     * @ORM\Embedded (class="ResetToken", columnPrefix="reset_token_")
     */
    private $resetToken;
    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    /**
     * @var Network[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Network", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $networks;

    /**
     * @var Role
     * @ORM\Column(type="user_role", length=16)
     */
    private $role;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column (type="date_immutable")
     */
    private $created_at;

    private function __construct(Id $id,\DateTimeImmutable $dateTimeImmutable)
    {
        $this->setId($id);
        $this->setDate($dateTimeImmutable);
        $this->setStatus(self::STATUS_NEW);
        $this->role = Role::user();
        $this->setNetworks(new ArrayCollection());
    }

    public static function signUpByEmail(Id $id,\DateTimeImmutable $date,Email $email, string $hash, string $token): self
    {
        $user= new self($id,$date);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status = self::STATUS_WAIT;
        return $user;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    public static function signUpByNetwork(Id $id,\DateTimeImmutable $date,string $network, string $identity): self
    {
        $user= new self($id,$date);
        $user->attachNetwork($network, $identity);
        $user->status = self::STATUS_ACTIVE;
        return $user;
    }

    private function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new Network($this, $network, $identity));
    }

    public function requestPasswordReset(ResetToken  $token,\DateTimeImmutable $date):void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if(!$this->email){
            throw  new \DomainException('Email is not specified.');
        }
        if($this->resetToken && !$this->resetToken->isExpiredTo($date)){
            throw new \DomainException('Resetting is already requested.');
        }
        $this->resetToken=$token;
    }

    public function changeRole(Role $role):void
    {
        if($this->role->isEqual($role)){
            throw new \DomainException('Role is already same.');
        }
        $this->role = $role;
    }

    public function passwordReset(\DateTimeImmutable $date, string $hash)
    {
        if(!$this->resetToken){
            throw new \DomainException('Resetting is not requested.');
        }
        if($this->resetToken->isExpiredTo($date)){
            throw new \DomainException('Reset token is expired.');
        }
        $this->passwordHash=$hash;
        $this->resetToken=null;
    }


    public function getId():Id
    {
        return $this->id;
    }

    public function setId(Id $id)
    {
        $this->id=$id;
    }

    public function getEmail():? Email
    {
        return $this->email;
    }

    public function setEmail(Email $email):void
    {
        $this->email=$email;
    }

    public function getPasswordHash():? string
    {
        return $this->passwordHash;
    }
    public function setPasswordHash(string $passwordHash):void
    {
         $this->passwordHash=$passwordHash;
    }

    public function getDate():\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setDate(\DateTimeImmutable $dateTimeImmutable)
    {
        $this->created_at=$dateTimeImmutable;
    }

    public function getResetToken():?ResetToken
    {
        return $this->resetToken;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getStatus():string
    {
        return $this->status;
    }

    public function setStatus(string $status):void
    {
        $this->status=$status;
    }

    public function getConfirmToken():?string
    {
        return $this->confirmToken;
    }

    public function setConfirmToken(string $confirmToken=null):void
    {
        $this->confirmToken=$confirmToken;
    }

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    public function setNetworks(ArrayCollection $networks)
    {
        $this->networks=$networks;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->resetToken->isEmpty()) {
            $this->resetToken = null;
        }
    }

}

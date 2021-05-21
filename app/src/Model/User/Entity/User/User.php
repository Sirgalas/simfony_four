<?php

namespace App\Model\User\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;

class User
{
    private const STATUS_NEW = 'new';
    private const STATUS_WAIT='wait';
    private const STATUS_ACTIVE='active';
    /**
     * @var Id
     */
    private $id;

    /**
     * @var Email|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $passwordHash;

    /**
     * @var string|null
     */
    private $confirmToken;

    /**
     * @var ResetToken|null
     */
    private $resetToken;
    /**
     * @var string
     */
    private $status;

    /**
     * @var Network[]|ArrayCollection
     */
    private $networks;

    /**
     * @var \DateTimeImmutable
     */
    private $created_at;

    public function __construct(Id $id,\DateTimeImmutable $dateTimeImmutable)
    {
        $this->setId($id);
        $this->setDate($dateTimeImmutable);
        $this->setStatus(self::STATUS_NEW);
        $this->setNetworks(new ArrayCollection());
    }

    public function signUpByEmail(Email $email, string $hash, string $token): void
    {
        if (!$this->isNew()) {
            throw new \DomainException('User is already signed up.');
        }
        $this->email = $email;
        $this->passwordHash = $hash;
        $this->confirmToken = $token;
        $this->status = self::STATUS_WAIT;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    public function signUpByNetwork(string $network, string $identity): void
    {
        if (!$this->isNew()) {
            throw new \DomainException('User is already signed up.');
        }
        $this->attachNetwork($network, $identity);
        $this->status = self::STATUS_ACTIVE;
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

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isNew():bool
    {
        return $this->status==self::STATUS_NEW;
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

}

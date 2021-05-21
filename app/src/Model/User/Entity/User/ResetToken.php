<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;

class ResetToken
{

    /**
     * @var string
     */
    private $token;

    /**
     * @var \DateTimeImmutable
     */
    private $expires;

    public function __construct(string $token, \DateTimeImmutable $expires)
    {
        $this->token = $token;
        $this->expires = $expires;
    }

    public function getToken():string
    {
        return $this->token;
    }

    public function setToken(string $token)
    {
        $this->token=$token;
    }

    public function getExpires():\DateTimeImmutable
    {
        return $this->expires;
    }

    public function setExpires(\DateTimeImmutable $expires)
    {
        $this->expires=$expires;
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }
}

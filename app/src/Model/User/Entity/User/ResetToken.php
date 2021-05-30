<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ResetToken
 * @package App\Model\User\Entity\User
 * @ORM\Embeddable
 */
class ResetToken
{

    /**
     * @var string
     * @ORM\Column (type="string",nullable=true)
     */
    private $token;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column (type="date_immutable", nullable=true)
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

    /**
     * @internal for postLoad callback
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->token);
    }
}

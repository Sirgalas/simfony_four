<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;
class Email
{

    private  $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Incorrect email.');
        }
        $this->setValue(mb_strtolower($value));
    }

    public function setValue($value)
    {
        $this->value=$value;
    }

    public function getValue():string
    {
        return $this->value;
    }

    public function isEqual(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}

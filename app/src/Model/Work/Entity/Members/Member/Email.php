<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Member;

use Webmozart\Assert\Assert;

class Email
{
    private  $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->setValue($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value):void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Incorrect email.');
        }
        $this->value = mb_strtolower($value);
    }

    public function isEqual(self $other):bool
    {
        return $this->getValue()===$other->getValue();
    }
}
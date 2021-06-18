<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use Webmozart\Assert\Assert;

class Id
{
    private $value;

    public function __construct(int $value)
    {
        Assert::notEmpty($value);
        $this->setValue($value);
    }

    public function setValue(int $value):void
    {
        $this->value=$value;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function __toString():string
    {
        return (string)$this->value;
    }
}
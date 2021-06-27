<?php
declare(strict_types=1);

namespace App\Model\Comment\Entity\Comment;

use Webmozart\Assert\Assert;

class AuthorId
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqualTo(self $id): bool
    {
        return $this->getValue() === $id->getValue();
    }
}
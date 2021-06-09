<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use PHPUnit\Framework\TestCase;
use App\Tests\Builder\User\UserBuilder;

class BlockTest extends TestCase
{
    /**
     * @test
     */
    public function success(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->block();

        self::assertFalse($user->isActive());
        self::assertTrue($user->isBlocked());
    }

    /**
     * @test
     */
    public function already(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->block();

        $this->expectExceptionMessage('User is already blocked.');
        $user->block();
    }
}
<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ActivateTest extends TestCase
{
    /**
     * @test
     */
    public function success(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->block();

        $user->activate();

        self::assertTrue($user->isActive());
        self::assertFalse($user->isBlocked());
    }

    /**
     * @test
     */
    public function already(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->activate();

        $this->expectExceptionMessage('User is already active.');
        $user->activate();
    }
}
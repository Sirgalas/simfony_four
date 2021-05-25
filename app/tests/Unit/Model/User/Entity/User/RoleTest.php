<?php


namespace App\Tests\Unit\Model\User\Entity\User;


use App\Model\User\Entity\User\Role;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    /**
     * @test
     */
    public function success(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->changeRole(Role::admin());

        self::assertFalse($user->getRole()->isUser());
        self::assertTrue($user->getRole()->isAdmin());
    }

    /**
     * @test
     */
    public function already(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $this->expectExceptionMessage('Role is already same.');

        $user->changeRole(Role::user());
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest
 * @package App\Tests\Unit\Model\User\Entity\User\Reset
 * @property User $user
 */
class RequestTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        $this->user=  $user = (new UserBuilder())->viaEmail()->confirmed()->build();
    }
    /**
     * @test
     */
    public function success(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $this->user->requestPasswordReset($token, $now);

        self::assertNotNull($this->user->getResetToken());
    }

    /**
     * @test
     */
    public function already(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $this->user->requestPasswordReset($token, $now);
        $this->expectExceptionMessage('Resetting is already requested.');
        $this->user->requestPasswordReset($token, $now);
    }

    /**
     * @test
     */
    public function expired(): void
    {
        $now = new \DateTimeImmutable();


        $token1 = new ResetToken('token', $now->modify('+1 day'));
        $this->user->requestPasswordReset($token1, $now);

        self::assertEquals($token1, $this->user->getResetToken());

        $token2 = new ResetToken('token', $now->modify('+3 day'));
        $this->user->requestPasswordReset($token2, $now->modify('+2 day'));

        self::assertEquals($token2, $this->user->getResetToken());
    }

    /**
     * @test
     */
    public function withoutEmail(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $user = (new UserBuilder())->viaNetwork()->build();
        $this->expectExceptionMessage('Email is not specified.');
        $user->requestPasswordReset($token, $now);
    }

    public function testNotConfirmed(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->viaEmail()->build();

        $this->expectExceptionMessage('User is not active.');
        $user->requestPasswordReset($token, $now);
    }

}

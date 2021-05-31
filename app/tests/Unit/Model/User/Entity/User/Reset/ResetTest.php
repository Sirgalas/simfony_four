<?php


namespace App\Tests\Unit\Model\User\Entity\User\Reset;


use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class ResetTest
 * @package App\Tests\Unit\Model\User\Entity\User\Reset
 */
class ResetTest extends TestCase
{

    /**
     * @test
     */
    public function success()
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());

        $user->passwordReset($now, $hash = 'hash');

        self::assertNull($user->getResetToken());
        self::assertEquals($hash, $user->getPasswordHash());
    }

    /**
     * @test
     */
    public function expiredToken():void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now);

        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Reset token is expired.');
        $user->passwordReset($now->modify('+1 day'), 'hash');
    }

    /**
     * @test
     */
    public function notRequest():void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $now = new \DateTimeImmutable();

        $this->expectExceptionMessage('Resetting is not requested.');
        $user->passwordReset($now, 'hash');
    }


}
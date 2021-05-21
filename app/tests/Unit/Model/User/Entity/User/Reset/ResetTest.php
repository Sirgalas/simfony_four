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
 * @property User $user
 */
class ResetTest extends TestCase
{

    private $user;

    public function setUp(): void
    {
        $this->user=  $user = (new UserBuilder())->viaEmail()->confirmed()->build();
    }

    /**
     * @test
     */
    public function success()
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token',$now->modify('+1 day'));

        $this->user->requestPasswordReset($token,$now);
        self::assertNotNull($this->user->getResetToken());
        $this->user->passwordReset($now,$hash='hash');
        self::assertNull($this->user->getResetToken());
    }

    /**
     * @test
     */
    public function expiredToken():void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now);

        $this->user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Reset token is expired.');
        $this->user->passwordReset($now->modify('+1 day'), 'hash');
    }

    /**
     * @test
     */
    public function notRequest():void
    {
        $now = new \DateTimeImmutable();
        $this->expectExceptionMessage('Resetting is not requested.');
        $this->user->passwordReset($now, 'hash');
    }


}

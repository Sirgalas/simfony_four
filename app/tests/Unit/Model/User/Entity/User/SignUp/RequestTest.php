<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;

/**
 * Class RequestTest
 * @package App\Tests\Unit\Model\User\Entity\User\SignUp
 * @property User $user
 */
class RequestTest extends TestCase
{

    private $user;

    public function setUp(): void
    {
        $this->user=  $user = (new UserBuilder())->build();
    }
    /**
     * @test
     */
    public function createSuccess(): void
    {

        $this->user->signUpByEmail(
            $email = new Email('test@app.test'),
            $hash = 'hash',
            $token='token'
        );

        self::assertEquals($email, $this->user->getEmail());
        self::assertEquals($hash, $this->user->getPasswordHash());
        self::assertTrue($this->user->isWait());
        self::assertFalse($this->user->isActive());
        self::assertEquals($token, $this->user->getConfirmToken());
    }

    /**
     * @test
     */
    public function already():void
    {

        $this->user->signUpByEmail(
            $email= new Email('tesr@app.test'),
            $hash='hash',
            $token='token'
        );
        $this->expectExceptionMessage('User is already signed up.');
        $this->user->signUpByEmail($email,$hash,$token);
    }
}

<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Email;

use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * @test
     */
    public function success(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->requestEmailChanging(
            $email = new Email('new@app.test'),
            $token = 'token'
        );

        self::assertEquals($email, $user->getNewEmail());
        self::assertEquals($token, $user->getNewEmailToken());
    }

    /**
     * @test
     */
    public function same(): void
    {
        $user = (new UserBuilder())
            ->viaEmail($email = new Email('new@app.test'))
            ->confirmed()->build();

        $this->expectExceptionMessage('Email is already same.');
        $user->requestEmailChanging($email, 'token');
    }

    /**
     * @test
     */
    public function notConfirmed(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $this->expectExceptionMessage('User is not active.');
        $user->requestEmailChanging(new Email('new@app.test'), 'token');
    }
}
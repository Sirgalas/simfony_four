<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Name;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;

/**
 * Class RequestTest
 * @package App\Tests\Unit\Model\User\Entity\User\SignUp
 */
class RequestTest extends TestCase
{

    /**
     * @test
     */
    public function createSuccess(): void
    {

        $user=User::signUpByEmail(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $name = new Name('First','Last'),
            $email = new Email('test@app.test'),
            $hash = 'hash',
            $token='token'
        );

        self::assertEquals($email, $user->getEmail());
        self::assertEquals($hash, $user->getPasswordHash());
        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($name, $user->getName());
        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());
        self::assertEquals($token, $user->getConfirmToken());
        self::assertTrue($user->getRole()->isUser());
    }

}

<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;

/**
 * Class ConfirmTest
 * @package App\Tests\Unit\Model\User\Entity\User\SignUp
 * @property User $user
 */
class ConfirmTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        $this->user=  $user = (new UserBuilder())->viaEmail()->build();
    }

    /**
     * @test
     */
    public function confirmSuccess():void
    {
        $this->user->confirmSignup();

        self::assertTrue($this->user->isActive());
        self::assertFalse($this->user->isWait());
        self::assertNull($this->user->getConfirmToken());
    }

    /**
     * @test
     */
    public function confirmAlready():void
    {
        $this->user->confirmSignup();
        $this->expectExceptionMessage('User is already confirmed.');
        $this->user->confirmSignup();
    }


}

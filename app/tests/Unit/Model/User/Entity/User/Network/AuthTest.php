<?php


namespace App\Tests\Unit\Model\User\Entity\User\Network;


use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * Class AuthTest
 * @package App\Tests\Unit\Model\User\Entity\User\Network
 * @property User $user
 */
class AuthTest extends TestCase
{

    private $user;

    public function setUp(): void
    {
        $this->user=  $user = (new UserBuilder())->build();
    }

    /**
     * @test
     */
    public function success():void
    {

        $this->user->signUpByNetwork(
            $network='vk',
            $identity='0000001'
        );
        self::assertTrue($this->user->isActive());

        self::assertCount(1, $networks = $this->user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals($network, $first->getNetwork());
        self::assertEquals($identity, $first->getIdentity());
    }

    /**
     * @test
     */
    public function already():void
    {

        $this->user->signUpByNetwork(
            $network='vk',
            $identity='0000001'
        );
        $this->expectExceptionMessage('User is already signed up.');
        $this->user->signUpByNetwork($network,$identity);
    }
}

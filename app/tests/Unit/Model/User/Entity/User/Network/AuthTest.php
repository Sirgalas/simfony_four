<?php


namespace App\Tests\Unit\Model\User\Entity\User\Network;


use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;


class AuthTest extends TestCase
{

    /**
     * @test
     */
    public function success():void
    {

        $user=User::signUpByNetwork(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $name = new Name('First', 'Last'),
            $network='vk',
            $identity='0000001'
        );
        self::assertTrue($user->isActive());
        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($name, $user->getName());
        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals($network, $first->getNetwork());
        self::assertEquals($identity, $first->getIdentity());
    }

}

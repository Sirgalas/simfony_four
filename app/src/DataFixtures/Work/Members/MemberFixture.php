<?php
declare(strict_types=1);

namespace App\DataFixtures\Work\Members;

use App\DataFixtures\UserFixtures;
use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Member\Email;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\Name;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MemberFixture extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_ADMIN = 'work_member_admin';

    public function load(ObjectManager $manager): void
    {
        /**
         * @var User $admin
         * @var User $user
         */
        $admin = $this->getReference(UserFixtures::REFERENCE_ADMIN);
        $user = $this->getReference(UserFixtures::REFERENCE_USER);

        /**
         * @var Group $staff
         * @var Group $customers
         */
        $staff = $this->getReference(GroupFixture::REFERENCE_STAFF);
        $customers = $this->getReference(GroupFixture::REFERENCE_CUSTOMERS);

        $member = $this->createMember($admin, $staff);
        $manager->persist($member);
        $this->setReference(self::REFERENCE_ADMIN, $member);

        $member = $this->createMember($user, $customers);
        $manager->persist($member);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            GroupFixture::class,
        ];
    }

    private function createMember(User $user, Group $group): Member
    {
        return new Member(
            new Id($user->getId()->getValue()),
            $group,
            new Name(
                $user->getName()->getFirst(),
                $user->getName()->getLast()
            ),
            new Email($user->getEmail() ? $user->getEmail()->getValue() : null)
        );
    }
}
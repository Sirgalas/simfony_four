<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\Work\Entity\Projects\Task;

use App\Tests\Builder\Work\Members\GroupBuilder;
use App\Tests\Builder\Work\Members\MemberBuilder;
use App\Tests\Builder\Work\Projects\ProjectBuilder;
use App\Tests\Builder\Work\Projects\TaskBuilder;
use PHPUnit\Framework\TestCase;

class ChangePriorityTest extends TestCase
{

    /**
     * @test
     */
    public function success(): void
    {
        $group = (new GroupBuilder())->build();
        $member = (new MemberBuilder())->build($group);
        $project = (new ProjectBuilder())->build();
        $task = (new TaskBuilder())->build($project, $member);

        $task->changePriority($member, new \DateTimeImmutable(),$priority = 3);

        self::assertEquals($priority, $task->getPriority());
    }

    /**
     * @test
     */
    public function already(): void
    {
        $group = (new GroupBuilder())->build();
        $member = (new MemberBuilder())->build($group);
        $project = (new ProjectBuilder())->build();
        $task = (new TaskBuilder())->build($project, $member);

        $task->changePriority($member, new \DateTimeImmutable(),$priority = 3);

        $this->expectExceptionMessage('Priority is already same.');
        $task->changePriority($member, new \DateTimeImmutable(),$priority);
    }

    /**
     * @test
     */
    public function incorrect(): void
    {
        $group = (new GroupBuilder())->build();
        $member = (new MemberBuilder())->build($group);
        $project = (new ProjectBuilder())->build();
        $task = (new TaskBuilder())->build($project, $member);

        $this->expectException(\InvalidArgumentException::class);
        $task->changePriority($member, new \DateTimeImmutable(),6);
    }
}
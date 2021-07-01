<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\Work\Entity\Projects\Task;

use App\Tests\Builder\Work\Members\GroupBuilder;
use App\Tests\Builder\Work\Members\MemberBuilder;
use App\Tests\Builder\Work\Projects\ProjectBuilder;
use App\Tests\Builder\Work\Projects\TaskBuilder;
use PHPUnit\Framework\TestCase;

class StartTest extends TestCase
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

        $task->assignExecutor($member);
        $task->start($date = new \DateTimeImmutable('+2 days'));

        self::assertTrue($task->isWorking());
        self::assertEquals($date, $task->getStartDate());
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

        $task->assignExecutor($member, new \DateTimeImmutable(), $member);
        $task->start($member, $date = new \DateTimeImmutable());

        $this->expectExceptionMessage('Task is already started.');
        $task->start($member, $date);
    }

    public function testWithoutExecutors(): void
    {
        $group = (new GroupBuilder())->build();
        $member = (new MemberBuilder())->build($group);
        $project = (new ProjectBuilder())->build();
        $task = (new TaskBuilder())->build($project, $member);

        $this->expectExceptionMessage('Task does not contain executors.');
        $task->start($member, new \DateTimeImmutable());
    }
}
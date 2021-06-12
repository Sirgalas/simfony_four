<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Members\Member\Reinstate;

class Handler
{
    private $members;
    private $flusher;

    public function __construct(MemberRepository $members, Flusher $flusher)
    {
        $this->members = $members;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $member = $this->members->get(new Id($command->id));

        $member->reinstate();

        $this->flusher->flush();
    }
}
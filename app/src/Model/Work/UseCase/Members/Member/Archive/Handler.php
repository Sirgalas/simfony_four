<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Members\Member\Archive;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;


class Handler
{
    private MemberRepository $members;
    private Flusher $flusher;

    public function __construct(MemberRepository $members, Flusher $flusher)
    {
        $this->members = $members;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $member = $this->members->get(new Id($command->id));

        $member->archive();

        $this->flusher->flush();
    }
}
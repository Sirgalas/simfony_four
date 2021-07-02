<?php
declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\EntityManagerInterface;

class Flusher
{
    private EntityManagerInterface $em;
    private EventDispatcher $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcher $dispatche){
        $this->em = $em;
        $this->dispatcher = $dispatche;
    }

    public function flush(AggregateRoot ...$roots):void
    {
        $this->em->flush();
        foreach ($roots as $root) {
            $this->dispatcher->dispatch($root->releaseEvents());
        }
    }
}

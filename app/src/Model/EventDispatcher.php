<?php
declare(strict_types=1);

namespace App\Model;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface EventDispatcher
{

    public function dispatch(array $events): void;
}
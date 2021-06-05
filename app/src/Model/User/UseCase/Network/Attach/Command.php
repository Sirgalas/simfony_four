<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Attach;


class Command
{
    /**
     * @var string
     */
    public string $user;
    /**
     * @var string
     */
    public string $network;
    /**
     * @var string
     */
    public string $identity;

    public function __construct(string $user, string $network, string $identity)
    {
        $this->user = $user;
        $this->network = $network;
        $this->identity = $identity;
    }
}
<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Create;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $name;
    /**
     * @var string[]
     */
    public $permissions;
}
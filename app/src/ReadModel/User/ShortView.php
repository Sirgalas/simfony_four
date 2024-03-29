<?php
declare(strict_types=1);

namespace App\ReadModel\User;


use App\ReadModel\AbstractCommand;

class ShortView extends AbstractCommand
{
    public $id;
    public $email;
    public $role;
    public $status;
}

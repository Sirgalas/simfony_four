<?php
declare(strict_types=1);

namespace App\ReadModel\User;


use App\ReadModel\AbstractCommand;

class DetailView extends AbstractCommand
{
    public $id;
    public $date;
    public $first_name;
    public $last_name;
    public $email;
    public $role;
    public $status;
    /**
     * @var NetworkView[]
     */
    public $networks;
}

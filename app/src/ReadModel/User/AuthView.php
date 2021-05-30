<?php
declare(strict_types=1);

namespace App\ReadModel\User;


use  App\ReadModel\AbstractCommand;
use Symfony\Component\Serializer\Annotation\Groups;

class AuthView extends AbstractCommand
{

    public $id;

    public $email;

    public  $password_hash;

    public $role;

    public $status;

}

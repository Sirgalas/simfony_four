<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Name
{
    /**
     * @var string
     * @ORM\Column (type="string")
     */
    private string $first;
    /**
     * @var string
     * @ORM\Column (type="string")
     */
    private string $last;

    public function __construct(string $first, string $last)
    {
        Assert::notEmpty($first);
        Assert::notEmpty($last);

        $this->setFirst($first);
        $this->setLast($last);
    }

    public function getFirst():string
    {
        return $this->first;
    }

    public function setFirst(string $first)
    {
        $this->first=$first;
    }

    public function getLast():string
    {
        return $this->last;
    }

    public function setLast(string $last)
    {
        $this->last=$last;
    }
}
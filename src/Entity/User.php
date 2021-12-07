<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDisplayRoles()
    {
        $roles = $this->getRoles();
        $string = '';
        $i = 0;
        foreach ($roles as $role) {
            if ($i > 0) {
                $string .= '<br>' . '- ' . $role;
            } else {
                $string .= '- ' . $role;
            }
            $i++;
        }
        return $string;
    }
}

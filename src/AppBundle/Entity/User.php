<?php

namespace AppBundle\Entity;


use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     *
     *@ORM\OneToMany(targetEntity="Advertisement", mappedBy="user", cascade={"persist"})
     */
    private $advertisement;

    public function __construct()
    {
        parent::__construct();
        $this->advertisement = new ArrayCollection();
        // your own logic
    }
}

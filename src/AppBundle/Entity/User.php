<?php

namespace AppBundle\Entity;


use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     *@ORM\OneToMany(targetEntity="Advertisement", mappedBy="users", cascade={"persist"})
     */
    private $advertisements;


    /**
     * @var string
     *
     * @ORM\Column(name="mailToken", type="string", length=32, nullable=true)
     */
    private $mailToken;


    public function __construct()
    {
        parent::__construct();
        $this->advertisements = new ArrayCollection();
        // your own logic
    }

    /**
     * @return string
     */
    public function getMailToken(): string
    {
        return $this->mailToken;
    }

    /**
     * @param string $mailToken
     */
    public function setMailToken(string $mailToken): void
    {
        $this->mailToken = $mailToken;
    }



    /**
     * Add advertisement.
     *
     * @param \AppBundle\Entity\Advertisement $advertisement
     *
     * @return User
     */
    public function addAdvertisement(\AppBundle\Entity\Advertisement $advertisement)
    {
        $this->advertisements[] = $advertisement;

        return $this;
    }

    /**
     * Remove advertisement.
     *
     * @param \AppBundle\Entity\Advertisement $advertisement
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAdvertisement(\AppBundle\Entity\Advertisement $advertisement)
    {
        return $this->advertisements->removeElement($advertisement);
    }

    /**
     * Get advertisements.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdvertisements()
    {
        return $this->advertisements;
    }
}

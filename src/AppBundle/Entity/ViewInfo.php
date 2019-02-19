<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name = "view_info")
 * @ORM\Entity
 */
class ViewInfo{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string")
     */
    protected $ip;

    /**
     * @ORM\ManyToOne(targetEntity="Advertisement", inversedBy="viewInfo")
     * @ORM\JoinColumn(name="id_advertisement", referencedColumnName="id")
     */
    protected $advertisement;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }


    /**
     * @param string $ip
     * @return ViewInfo
     */
    public function setIp(string $ip): ViewInfo
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdvertisement()
    {
        return $this->advertisement;
    }

    /**
     * @param mixed $advertisement
     */
    public function setAdvertisement($advertisement): void
    {
        $this->advertisement = $advertisement;
    }

}
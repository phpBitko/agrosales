<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="parsel")
 * @ORM\Entity
 *
 */
Class Parsel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="parcel_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     *
     */
    private $address;
    /**
     * @var integer
     * @ORM\Column(name="area", type="integer", nullable=false)
     *
     */
    private $area;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Parsel
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set area.
     *
     * @param int $area
     *
     * @return Parsel
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area.
     *
     * @return int
     */
    public function getArea()
    {
        return $this->area;
    }
}

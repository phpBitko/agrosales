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


}
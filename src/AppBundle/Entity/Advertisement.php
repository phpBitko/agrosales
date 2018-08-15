<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 10.07.2018
 * Time: 18:23
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Advertisement
 *
 * @ORM\Table(name="advertisement")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdvertisementRepository")
 */
class Advertisement
{
    const NUM_ITEMS=9;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="advertisement_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text_head", type="string", length=500, nullable=false)
     *
     */
    private $textHead;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var geometry
     * @ORM\Column(name="geom", type="geometry", nullable=true)
     */
    private $geom;

    /**
     * @var string
     * @ORM\Column(name="text_about", type="text", nullable=true)
     */
    private $textAbout;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     *
     */
    protected $email;

    /**
     * @var boolean
     * @ORM\Column(name="is_electricity", type="boolean", nullable=true)
     */
    private $isElectricity = false;
    /**
     * @var boolean
     * @ORM\Column(name="is_gas", type="boolean", nullable=true)
     */
    private $isGas = false;

    /**
     * @var boolean
     * @ORM\Column(name="is_water_supply", type="boolean", nullable=true)
     */
    private $isWaterSupply = false;

    /**
     * @var boolean
     * @ORM\Column(name="is_road", type="boolean", nullable=true)
     */
    private $isRoad = false;

    /**
     * @var boolean
     * @ORM\Column(name="is_archive", type="boolean", nullable=true)
     */
    private $isArchive = false;

    /**
     * @var boolean
     * @ORM\Column(name="is_sewerage", type="boolean", nullable=true)
     */
    private $isSewerage = false;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="area", type="float", precision=10, scale=0, nullable=true)
     */
    private $area;

    /**
     * @var string
     *
     * @ORM\Column(name="declarant_phone_num", type="string", length=20, nullable=true)
     */
    private $declarantPhoneNum;

    /**
     * @var integer
     *
     * @ORM\Column(name="area_unit", type="integer", nullable=true)
     */
    private $areaUnit = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="coord_b", type="float", precision=10, scale=0, nullable=true)
     */
    private $coordB;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_dir_district", type="integer", nullable=true)
     */
    private $idDirDistrict;
    /**
     *
     * @ORM\ManyToOne(targetEntity="DirDistrict", inversedBy="advertisement")
     * @ORM\JoinColumn(name="id_dir_district", referencedColumnName="id")
     */
    private $dirDistrict;
    /**
     * @var integer
     *
     * @ORM\Column(name="id_dir_region", type="integer", nullable=true)
     */
    private $idDirRegion;

    /**
     *
     * @ORM\ManyToOne(targetEntity="DirRegion", inversedBy="advertisement")
     * @ORM\JoinColumn(name="id_dir_region", referencedColumnName="id")
     */
    private $dirRegion;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer", nullable=true)
     */
    private $idUser;


    /**
     * @var integer
     *
     * @ORM\Column(name="id_purpose", type="integer", nullable=true)
     */
    private $idPurpose;
    /**
     *
     * @ORM\ManyToOne(targetEntity="DirPurpose", inversedBy="advertisement")
     * @ORM\JoinColumn(name="id_purpose", referencedColumnName="id")
     */
    private $dirPurpose;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="add_date", type="datetime", nullable=true)
     */
    private $addDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate;


    /**
     * @var float
     *
     * @ORM\Column(name="coord_l", type="float", precision=10, scale=0, nullable=true)
     */
    private $coordL;

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param float $area
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * @return \DateTime
     */
    public function getAddDate()
    {
        return $this->addDate;
    }

    /**
     * @param \DateTime $addDate
     */
    public function setAddDate($addDate)
    {
        $this->addDate = $addDate;
    }


}
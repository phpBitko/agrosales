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

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTextHead()
    {
        return $this->textHead;
    }

    /**
     * @param string $textHead
     */
    public function setTextHead($textHead)
    {
        $this->textHead = $textHead;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return geometry
     */
    public function getGeom()
    {
        return $this->geom;
    }

    /**
     * @param geometry $geom
     */
    public function setGeom($geom)
    {
        $this->geom = $geom;
    }

    /**
     * @return string
     */
    public function getTextAbout()
    {
        return $this->textAbout;
    }

    /**
     * @param string $textAbout
     */
    public function setTextAbout($textAbout)
    {
        $this->textAbout = $textAbout;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function isElectricity()
    {
        return $this->isElectricity;
    }

    /**
     * @param bool $isElectricity
     */
    public function setIsElectricity($isElectricity)
    {
        $this->isElectricity = $isElectricity;
    }

    /**
     * @return bool
     */
    public function isGas()
    {
        return $this->isGas;
    }

    /**
     * @param bool $isGas
     */
    public function setIsGas($isGas)
    {
        $this->isGas = $isGas;
    }

    /**
     * @return bool
     */
    public function isWaterSupply()
    {
        return $this->isWaterSupply;
    }

    /**
     * @param bool $isWaterSupply
     */
    public function setIsWaterSupply($isWaterSupply)
    {
        $this->isWaterSupply = $isWaterSupply;
    }

    /**
     * @return bool
     */
    public function isRoad()
    {
        return $this->isRoad;
    }

    /**
     * @param bool $isRoad
     */
    public function setIsRoad($isRoad)
    {
        $this->isRoad = $isRoad;
    }

    /**
     * @return bool
     */
    public function isArchive()
    {
        return $this->isArchive;
    }

    /**
     * @param bool $isArchive
     */
    public function setIsArchive($isArchive)
    {
        $this->isArchive = $isArchive;
    }

    /**
     * @return bool
     */
    public function isSewerage()
    {
        return $this->isSewerage;
    }

    /**
     * @param bool $isSewerage
     */
    public function setIsSewerage($isSewerage)
    {
        $this->isSewerage = $isSewerage;
    }

    /**
     * @return string
     */
    public function getDeclarantPhoneNum()
    {
        return $this->declarantPhoneNum;
    }

    /**
     * @param string $declarantPhoneNum
     */
    public function setDeclarantPhoneNum($declarantPhoneNum)
    {
        $this->declarantPhoneNum = $declarantPhoneNum;
    }

    /**
     * @return int
     */
    public function getAreaUnit()
    {
        return $this->areaUnit;
    }

    /**
     * @param int $areaUnit
     */
    public function setAreaUnit($areaUnit)
    {
        $this->areaUnit = $areaUnit;
    }

    /**
     * @return float
     */
    public function getCoordB()
    {
        return $this->coordB;
    }

    /**
     * @param float $coordB
     */
    public function setCoordB($coordB)
    {
        $this->coordB = $coordB;
    }

    /**
     * @return int
     */
    public function getIdDirDistrict()
    {
        return $this->idDirDistrict;
    }

    /**
     * @param int $idDirDistrict
     */
    public function setIdDirDistrict($idDirDistrict)
    {
        $this->idDirDistrict = $idDirDistrict;
    }

    /**
     * @return mixed
     */
    public function getDirDistrict()
    {
        return $this->dirDistrict;
    }

    /**
     * @param mixed $dirDistrict
     */
    public function setDirDistrict($dirDistrict)
    {
        $this->dirDistrict = $dirDistrict;
    }

    /**
     * @return int
     */
    public function getIdDirRegion()
    {
        return $this->idDirRegion;
    }

    /**
     * @param int $idDirRegion
     */
    public function setIdDirRegion($idDirRegion)
    {
        $this->idDirRegion = $idDirRegion;
    }

    /**
     * @return mixed
     */
    public function getDirRegion()
    {
        return $this->dirRegion;
    }

    /**
     * @param mixed $dirRegion
     */
    public function setDirRegion($dirRegion)
    {
        $this->dirRegion = $dirRegion;
    }

    /**
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param int $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    /**
     * @return int
     */
    public function getIdPurpose()
    {
        return $this->idPurpose;
    }

    /**
     * @param int $idPurpose
     */
    public function setIdPurpose($idPurpose)
    {
        $this->idPurpose = $idPurpose;
    }

    /**
     * @return mixed
     */
    public function getDirPurpose()
    {
        return $this->dirPurpose;
    }

    /**
     * @param mixed $dirPurpose
     */
    public function setDirPurpose($dirPurpose)
    {
        $this->dirPurpose = $dirPurpose;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @param \DateTime $updateDate
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
    }

    /**
     * @return float
     */
    public function getCoordL()
    {
        return $this->coordL;
    }

    /**
     * @param float $coordL
     */
    public function setCoordL($coordL)
    {
        $this->coordL = $coordL;
    }



}
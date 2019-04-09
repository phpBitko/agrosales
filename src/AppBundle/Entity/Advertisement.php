<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 10.07.2018
 * Time: 18:23
 */

namespace AppBundle\Entity;

use AppBundle\Entity\Interfaces\InstanceUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
//Використовуєтся для бандла FileUploader
use Symfony\Component\HttpFoundation\File\File;
use Jsor\Doctrine\PostGIS\Functions\ST_GeogFromText;


/**
 * Advertisement
 *
 * @ORM\Table(name="advertisement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdvertisementRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Advertisement implements InstanceUserInterface
{

    /**
     * Кількість оголошень на сторінку
     */
    const NUM_ITEMS = 9;

    /**
     * Змінна для параметрів сортування по замовчуванню
     *
     * @var array
     */
    public static $order = ['isTop' => 'DESC', 'addDate' => 'DESC'];

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
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 150,
     *      maxMessage = "Довжина тексту не повинна перевищувати {{ limit }} символів"
     * )
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
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 4000,
     *      maxMessage = "Довжина тексту не повинна перевищувати {{ limit }} символів"
     * )
     */
    private $textAbout;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     *
     */
    protected $email;

    /**
     * @var boolean
     * @ORM\Column(name="is_top",type="boolean", nullable=false)
     *
     */
    private $isTop = false;

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
     * @var bool
     * @ORM\Column(name="is_house", type="boolean", nullable=true)
     */
    private $isHouse = false;

    /**
     * @return bool
     */
    public function isHouse(): bool
    {
        return $this->isHouse;
    }

    /**
     * @param bool $isHouse
     */
    public function setIsHouse(bool $isHouse): void
    {
        $this->isHouse = $isHouse;
    }


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
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="integer",
     *     message="Допустимі тільки цілі числа."
     * )
     *
     * @Assert\Range(
     *     min = 0,
     *     max = 2147483647,
     *     minMessage = "Значення повинно бути не менше {{ limit }}",
     *     maxMessage  = "Значення повинно бути не більше {{ limit }}."
     * )
     *
     */
    private $price;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="float",
     * )
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
     * @var string
     *
     * @ORM\Column(name="cadastral_number", type="string", length=22, nullable=true)
     */
    private $cadastralNumber;

    /**
     * @return mixed
     */
    public function getCadastralNumber()
    {
        return $this->cadastralNumber;
    }

    /**
     * @param mixed $cadastralNumber
     */
    public function setCadastralNumber($cadastralNumber): void
    {
        $this->cadastralNumber = $cadastralNumber;
    }


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
     * @ORM\ManyToOne(targetEntity="DirDistrict")
     * @ORM\JoinColumn(name="id_dir_district", referencedColumnName="id")
     */
    private $dirDistrict;

    /**
     *
     * @ORM\ManyToOne(targetEntity="DirStatus")
     * @ORM\JoinColumn(name="id_dir_status", referencedColumnName="id")
     */
    private $dirStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="view_count", type="integer", nullable=true)
     */
    private $viewCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer", nullable=true)
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="advertisements")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Photos", mappedBy="advertisement", cascade={"persist"}, orphanRemoval=true)
     */
    private $photos;

    /**
     * @ORM\OneToMany(targetEntity="ViewInfo", mappedBy="advertisement", cascade={"persist"}, orphanRemoval=true)
     */
    private $viewInfo;

    /**
     * @ORM\OneToMany(targetEntity="Messages", mappedBy="advertisement", cascade={"persist"}, orphanRemoval=true)
     */
    private $messages;

    /**
     *
     * @ORM\ManyToOne(targetEntity="DirRegion")
     * @ORM\JoinColumn(name="id_dir_region", referencedColumnName="id", nullable=true)
     */
    private $dirRegion;

    /**
     *
     * @ORM\ManyToOne(targetEntity="DirPurpose")
     * @ORM\JoinColumn(name="id_dir_purpose", referencedColumnName="id")
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
     * Constructor
     */
    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->addDate = new \DateTime();
    }

    /**
     * @return int
     */
    public function getViewCount(): ?int
    {
        return $this->viewCount;
    }

    /**
     * @param int $viewCount
     */
    public function setViewCount(int $viewCount): void
    {
        $this->viewCount = $viewCount;
    }

    /**
     * @return mixed
     */
    public function getViewInfo()
    {
        return $this->viewInfo;
    }

    /**
     * @param mixed $viewInfo
     */
    public function setViewInfo($viewInfo): void
    {
        $this->viewInfo = $viewInfo;
    }

    /**
     * @return bool
     */
    public function isTop(): bool
    {
        return $this->isTop;
    }

    /**
     * @param bool $isTop
     */
    public function setIsTop(bool $isTop): void
    {
        $this->isTop = $isTop;
    }

    /**
     * @return int
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param int $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return mixed
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param mixed $messages
     */
    public function setMessages($messages): void
    {
        $this->messages = $messages;
    }


    /**
     * @return mixed
     */
    public function getDirStatus()
    {
        return $this->dirStatus;
    }

    /**
     * @param mixed $dirStatus
     */
    public function setDirStatus($dirStatus): void
    {
        $this->dirStatus = $dirStatus;
    }


    /**
     * @return ArrayCollection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param mixed $photos
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }


    public function addPhoto(Photos $photo)
    {
        $photo->setAdvertisement($this);
        $this->photos[] = $photo;

        return $this;
    }

    public function removePhoto(Photos $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
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
     * @ORM\PreFlush
     */
    public function doStuffOnPostPersist()
    {
        if (!empty($this->getCoordB()) && !empty($this->getCoordL())) {
            $this->setGeom('point(' . $this->getCoordB() . ' ' . $this->getCoordL() . ')');
        }
    }


    /**
     * @param int $idDirPurpose
     */
    public function setDirIdPurpose($idDirPurpose)
    {
        $this->idDirPurpose = $idDirPurpose;
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


    /**
     * Get isElectricity.
     *
     * @return bool|null
     */
    public function getIsElectricity()
    {
        return $this->isElectricity;
    }

    /**
     * Get isGas.
     *
     * @return bool|null
     */
    public function getIsGas()
    {
        return $this->isGas;
    }

    /**
     * Get isWaterSupply.
     *
     * @return bool|null
     */
    public function getIsWaterSupply()
    {
        return $this->isWaterSupply;
    }

    /**
     * Get isRoad.
     *
     * @return bool|null
     */
    public function getIsRoad()
    {
        return $this->isRoad;
    }

    /**
     * Get isArchive.
     *
     * @return bool|null
     */
    public function getIsArchive()
    {
        return $this->isArchive;
    }

    /**
     * Get isSewerage.
     *
     * @return bool|null
     */
    public function getIsSewerage()
    {
        return $this->isSewerage;
    }
}

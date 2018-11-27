<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 10.07.2018
 * Time: 17:32
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Photo
 *
 * @ORM\Table(name="photos", indexes={@ORM\Index(name="IDX_14B784187777BB8B", columns={"id_advertisement"})})
 * @ORM\Entity
 */
class Photos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="photos_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="photo_path", type="string", length=2000, nullable=true)
     */
    private $photoPath;
    /**
     * @var string
     *
     * @ORM\Column(name="photo_name_new",type="string",length=2000,nullable=true)
     */
    private $photoNameNew;

    /**
     * @return mixed
     */
    public function getPhotoNameNew()
    {
        return $this->photoNameNew;
    }

    /**
     * @param mixed $photoNameNew
     */
    public function setPhotoNameNew($photoNameNew)
    {
        $this->photoNameNew = $photoNameNew;
    }


    /**
     * @var string
     *
     * @ORM\Column(name="photo_name_original", type="string", length=2000, nullable=true)
     */
    private $photoNameOriginal;

    /**
     * @return string
     */
    public function getPhotoNameOriginal()
    {
        return $this->photoNameOriginal;
    }

    /**
     * @param string $photoNameOriginal
     */
    public function setPhotoNameOriginal($photoNameOriginal)
    {
        $this->photoNameOriginal = $photoNameOriginal;
    }


    /**
     * @var  \DateTime
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $addDate;



    /**
     * @return mixed
     */
    public function getAddDate()
    {
        return $this->addDate;
    }

    /**
     * @param mixed $addDate
     */
    public function setAddDate($addDate)
    {
        $this->addDate = $addDate;
    }


    /**
     * @ORM\ManyToOne(targetEntity="Advertisement", inversedBy="photos")
     * @ORM\JoinColumn(name="id_advertisement", nullable=true, referencedColumnName="id")
     */
    private $advertisement;

    /**
     * @return Advertisement
     */
    public function getAdvertisement()
    {
        return $this->advertisement;
    }

    /**
     * @param Advertisement $advertisement
     */
    public function setAdvertisement($advertisement)
    {
        $this->advertisement = $advertisement;
    }


    /**
     * @var boolean
     *
     * @ORM\Column(name="is_archive", type="boolean", nullable=true)
     */

    private $isArchive = false;

    /**
     * @param bool $isArchive
     */
    public function setIsArchive($isArchive)
    {
        $this->isArchive = $isArchive;
    }


    /**
     * @return string
     */
    public function getPhotoPath()
    {
        return $this->photoPath;
    }

    /**
     * @param string $photoPath
     */
    public function setPhotoPath($photoPath)
    {
        $this->photoPath = $photoPath;
    }

//    /**
//     * @return \Advertisement
//     */
//    public function getIdAdvertisement() {
//        return $this->idAdvertisement;
//    }
//
//    /**
//     * @param \Advertisement $idAdvertisement
//     */
//    public function setIdAdvertisement($idAdvertisement) {
//        $this->idAdvertisement = $idAdvertisement;
//    }


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
     * Get isArchive.
     *
     * @return bool|null
     */
    public function getIsArchive()
    {
        return $this->isArchive;
    }

    public function __construct()
    {
        $this->addDate = new \DateTime;
    }
}

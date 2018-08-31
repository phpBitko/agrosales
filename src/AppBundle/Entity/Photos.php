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
     * @ORM\Column(name="photo_name", type="string", length=2000, nullable=true)
     */
    private $photoName;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_advertisement", type="integer")
     */
    private $idAdvertisement;
    /**
     * @ORM\ManyToOne(targetEntity="Advertisement", inversedBy="photos")
     * @ORM\JoinColumn(name="id_advertisement", nullable=false, referencedColumnName="id")
     */
    private $advertisement;


    /**
     * @var boolean
     *
     * @ORM\Column(name="is_archive", type="boolean", nullable=true)
     */

    private $isArchive = false;

    /**
     * @param bool $isArchive
     */
    public function setIsArchive($isArchive) {
        $this->isArchive = $isArchive;
    }


    /**
     * @return string
     */
    public function getPhotoPath() {
        return $this->photoPath;
    }

    /**
     * @param string $photoPath
     */
    public function setPhotoPath($photoPath) {
        $this->photoPath = $photoPath;
    }

    /**
     * @return \Advertisement
     */
    public function getIdAdvertisement() {
        return $this->idAdvertisement;
    }

    /**
     * @param \Advertisement $idAdvertisement
     */
    public function setIdAdvertisement($idAdvertisement) {
        $this->idAdvertisement = $idAdvertisement;
    }

    /**
     * @return string
     */
    public function getPhotoName() {
        return $this->photoName;
    }

    /**
     * @param string $photoName
     */
    public function setPhotoName($photoName) {
        $this->photoName = $photoName;
    }
    /**
     * @return \Advertisement
     */
    public function getAdvertisement() {
        return $this->advertisement;
    }

    /**
     * @param \Advertisement $advertisement
     */
    public function setAdvertisement($advertisement) {
        $this->advertisement = $advertisement;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
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
}

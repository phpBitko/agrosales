<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 10.07.2018
 * Time: 17:32
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Photo
 *
 * @ORM\Table(name="photos", indexes={@ORM\Index(name="IDX_14B784187777BB8B", columns={"id_advertisement"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Photos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="photos_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="photo_name_original", type="string", length=255, nullable=true)
     */
    private $photoNameOriginal;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @ORM\ManyToOne(targetEntity="Advertisement", inversedBy="photos")
     * @ORM\JoinColumn(name="id_advertisement", referencedColumnName="id")
     */
    private $advertisement;

    /**
     * @var string
     *
     * @ORM\Column(name="photo_name_new",type="string",length=2000, nullable=true)
     */
    private $photoNameNew;

    /**
     * @var  \DateTime
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $addDate;

    /**
     * @return mixed
     */
    public function getPhotoNameNew()
    {
        return $this->photoNameNew;
    }

    /**
     * @return string
     */
    public function getPhotoNameOriginal()
    {
        return $this->photoNameOriginal;
    }

    /**
     * @param mixed $photoNameNew
     */
    public function setPhotoNameNew($photoNameNew)
    {
        $this->photoNameNew = $photoNameNew;
    }

    /**
     * @param string $photoNameOriginal
     */
    public function setPhotoNameOriginal($photoNameOriginal)
    {
        $this->photoNameOriginal = $photoNameOriginal;
    }

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
     * @return Advertisement
     */
    public function getAdvertisement()
    {
        return $this->advertisement;
    }


    /**
     * @param Advertisement $advertisement
     * @return Advertisement
     */
    public function setAdvertisement(Advertisement $advertisement)
    {
        $this->advertisement = $advertisement;

        return $this;
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


    public function __construct()
    {
        $this->addDate = new \DateTime;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }


    /**
     * @param string $extension
     * @return $this
     */
    public function setExtension(string $extension)
    {
        $this->extension = $extension;

        return $this;
    }

    private $file;

    private $tempFilename;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        // Replacing a file ? Check if we already have a file for this entity
        if (null !== $this->extension) {
            // Save file extension so we can remove it later
            $this->tempFilename = $this->extension;

            // Reset values
            $this->extension = null;
            $this->photoNameOriginal = null;
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        // If no file is set, do nothing
        if (null === $this->file) {
            return;
        }

        // The file name is the entity's ID
        // We also need to store the file extension
        $this->extension = $this->file->guessExtension();
        // And we keep the original name
        $this->photoNameOriginal = $this->file->getClientOriginalName();
        $this->photoNameNew = uniqid() . '.' . $this->extension;
    }


    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // If no file is set, do nothing
        if (null === $this->file) {
            return;
        }

        // A file is present, remove it
        if (null !== $this->tempFilename) {
            $oldFile = $this->getUploadRootDir() . '/' . $this->id . '.' . $this->tempFilename;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // Move the file to the upload folder
        $this->file->move(
            $this->getUploadRootDir(),
            $this->photoNameNew
        );
    }



    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        // Save the name of the file we would want to remove
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->extension;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        // PostRemove => We no longer have the entity's ID => Use the name we saved
        if (file_exists($this->tempFilename))
        {
            // Remove file
            unlink($this->tempFilename);
        }
    }

    public function getUploadDir()
    {
        // Upload directory
        return 'resources/public/picture/move/';
        // This means /web/uploads/documents/
    }

    protected function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        // Image location (PHP)
        return __DIR__.'/../'.$this->getUploadDir().'/'.$this->getAddDate()->format('Y-m-d').'/'.$this->getAdvertisement()->getId();
    }

    public function getUrl()
    {
        return $this->id.'.'.$this->extension;
    }


}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DirStatus
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessagesRepository")
 */
class Messages
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="messages_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_view", type="boolean", nullable=false)
     *
     */
    private $isView = false;

    /**
     * @ORM\ManyToOne(targetEntity="Advertisement", inversedBy="messages")
     * @ORM\JoinColumn(name="id_advertisement", referencedColumnName="id")
     */
    private $advertisement;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    private $users;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="add_date", type="datetime", nullable=false)
     */
    private $addDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addDate = new \DateTime();
    }

    /**
     * @return bool
     */
    public function isView(): bool
    {
        return $this->isView;
    }

    /**
     * @param bool $isView
     */
    public function setIsView(bool $isView): void
    {
        $this->isView = $isView;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
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

    /**
     * @return \DateTime
     */
    public function getAddDate(): \DateTime
    {
        return $this->addDate;
    }

    /**
     * @param \DateTime $addDate
     */
    public function setAddDate(\DateTime $addDate): void
    {
        $this->addDate = $addDate;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate(): \DateTime
    {
        return $this->updateDate;
    }

    /**
     * @param \DateTime $updateDate
     */
    public function setUpdateDate(\DateTime $updateDate): void
    {
        $this->updateDate = $updateDate;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
    }






}
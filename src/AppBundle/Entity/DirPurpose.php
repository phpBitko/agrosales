<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 10.07.2018
 * Time: 17:29
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * DirPurpose
 *
 * @ORM\Table(name="dir_purpose")
 * @ORM\Entity
 */
class DirPurpose
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="dir_purpose_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255, nullable=true)
     */
    private $text;
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string",length=10,nullable=true)
     */
    private $code;

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * @return string
     */
    public function getText() {
        return $this->text;
    }


    /**
     * @param string $text
     */
    public function setText($text) {
        $this->text = $text;
    }


}

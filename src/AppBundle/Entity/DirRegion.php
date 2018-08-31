<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 10.07.2018
 * Time: 17:11
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * OblNew
 *
 * @ORM\Table(name="dir_region", indexes={@ORM\Index(name="obl_new_koatuu_t", columns={"koatuu_t"}), @ORM\Index(name="obl_new_the_geom_4284", columns={"the_geom_42_bl"}), @ORM\Index(name="obl_new_koatuu", columns={"koatuu"}), @ORM\Index(name="obl_new_the_geom_900913", columns={"the_geom_900913"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OblNewRepository")
 */
class DirRegion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="dir_region_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="gid", type="integer", nullable=true)
     */
    private $gid;

    /**
     * @var string
     *
     * @ORM\Column(name="koatuu", type="string", length=10, nullable=true)
     */
    private $koatuu;

    /**
     * @var string
     *
     * @ORM\Column(name="natoobl", type="string", length=30, nullable=true)
     */
    private $natoobl;

    /**
     * @var float
     *
     * @ORM\Column(name="patoobl", type="float", precision=10, scale=0, nullable=true)
     */
    private $patoobl;

    /**
     * @var string
     *
     * @ORM\Column(name="vpor", type="string", length=100, nullable=true)
     */
    private $vpor;

    /**
     * @var string
     *
     * @ORM\Column(name="vposs", type="string", length=100, nullable=true)
     */
    private $vposs;

    /**
     * @var string
     *
     * @ORM\Column(name="mpd", type="string", length=100, nullable=true)
     */
    private $mpd;

    /**
     * @var string
     *
     * @ORM\Column(name="datep", type="string", length=100, nullable=true)
     */
    private $datep;

    /**
     * @var geometry
     *
     * @ORM\Column(name="the_geom", type="geometry", nullable=true)
     */
    private $theGeom;

    /**
     * @var integer
     *
     * @ORM\Column(name="coordsys_geom", type="integer", nullable=true)
     */
    private $coordsysGeom;

    /**
     * @var geometry
     *
     * @ORM\Column(name="the_geom_42_bl", type="geometry", nullable=true)
     */
    private $theGeom42Bl;

    /**
     * @var geometry
     *
     * @ORM\Column(name="the_geom_4326", type="geometry", nullable=true)
     */
    private $theGeom4326;

    /**
     * @var geometry
     *
     * @ORM\Column(name="the_geom_900913", type="geometry", nullable=true)
     */
    private $theGeom900913;

    /**
     * @var string
     *
     * @ORM\Column(name="koatuu_t", type="string", length=10, nullable=true)
     */
    private $koatuuT;

    /**
     * @var float
     *
     * @ORM\Column(name="x_min", type="float", precision=10, scale=0, nullable=true)
     */
    private $xMin;

    /**
     * @var float
     *
     * @ORM\Column(name="y_min", type="float", precision=10, scale=0, nullable=true)
     */
    private $yMin;

    /**
     * @var float
     *
     * @ORM\Column(name="x_max", type="float", precision=10, scale=0, nullable=true)
     */
    private $xMax;

    /**
     * @var float
     *
     * @ORM\Column(name="y_max", type="float", precision=10, scale=0, nullable=true)
     */
    private $yMax;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=50, nullable=true)
     */
    private $info;

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
     * Set gid.
     *
     * @param int|null $gid
     *
     * @return DirRegion
     */
    public function setGid($gid = null)
    {
        $this->gid = $gid;

        return $this;
    }

    /**
     * Get gid.
     *
     * @return int|null
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * Set koatuu.
     *
     * @param string|null $koatuu
     *
     * @return DirRegion
     */
    public function setKoatuu($koatuu = null)
    {
        $this->koatuu = $koatuu;

        return $this;
    }

    /**
     * Get koatuu.
     *
     * @return string|null
     */
    public function getKoatuu()
    {
        return $this->koatuu;
    }

    /**
     * Set natoobl.
     *
     * @param string|null $natoobl
     *
     * @return DirRegion
     */
    public function setNatoobl($natoobl = null)
    {
        $this->natoobl = $natoobl;

        return $this;
    }

    /**
     * Get natoobl.
     *
     * @return string|null
     */
    public function getNatoobl()
    {
        return $this->natoobl;
    }

    /**
     * Set patoobl.
     *
     * @param float|null $patoobl
     *
     * @return DirRegion
     */
    public function setPatoobl($patoobl = null)
    {
        $this->patoobl = $patoobl;

        return $this;
    }

    /**
     * Get patoobl.
     *
     * @return float|null
     */
    public function getPatoobl()
    {
        return $this->patoobl;
    }

    /**
     * Set vpor.
     *
     * @param string|null $vpor
     *
     * @return DirRegion
     */
    public function setVpor($vpor = null)
    {
        $this->vpor = $vpor;

        return $this;
    }

    /**
     * Get vpor.
     *
     * @return string|null
     */
    public function getVpor()
    {
        return $this->vpor;
    }

    /**
     * Set vposs.
     *
     * @param string|null $vposs
     *
     * @return DirRegion
     */
    public function setVposs($vposs = null)
    {
        $this->vposs = $vposs;

        return $this;
    }

    /**
     * Get vposs.
     *
     * @return string|null
     */
    public function getVposs()
    {
        return $this->vposs;
    }

    /**
     * Set mpd.
     *
     * @param string|null $mpd
     *
     * @return DirRegion
     */
    public function setMpd($mpd = null)
    {
        $this->mpd = $mpd;

        return $this;
    }

    /**
     * Get mpd.
     *
     * @return string|null
     */
    public function getMpd()
    {
        return $this->mpd;
    }

    /**
     * Set datep.
     *
     * @param string|null $datep
     *
     * @return DirRegion
     */
    public function setDatep($datep = null)
    {
        $this->datep = $datep;

        return $this;
    }

    /**
     * Get datep.
     *
     * @return string|null
     */
    public function getDatep()
    {
        return $this->datep;
    }

    /**
     * Set theGeom.
     *
     * @param geometry|null $theGeom
     *
     * @return DirRegion
     */
    public function setTheGeom($theGeom = null)
    {
        $this->theGeom = $theGeom;

        return $this;
    }

    /**
     * Get theGeom.
     *
     * @return geometry|null
     */
    public function getTheGeom()
    {
        return $this->theGeom;
    }

    /**
     * Set coordsysGeom.
     *
     * @param int|null $coordsysGeom
     *
     * @return DirRegion
     */
    public function setCoordsysGeom($coordsysGeom = null)
    {
        $this->coordsysGeom = $coordsysGeom;

        return $this;
    }

    /**
     * Get coordsysGeom.
     *
     * @return int|null
     */
    public function getCoordsysGeom()
    {
        return $this->coordsysGeom;
    }

    /**
     * Set theGeom42Bl.
     *
     * @param geometry|null $theGeom42Bl
     *
     * @return DirRegion
     */
    public function setTheGeom42Bl($theGeom42Bl = null)
    {
        $this->theGeom42Bl = $theGeom42Bl;

        return $this;
    }

    /**
     * Get theGeom42Bl.
     *
     * @return geometry|null
     */
    public function getTheGeom42Bl()
    {
        return $this->theGeom42Bl;
    }

    /**
     * Set theGeom4326.
     *
     * @param geometry|null $theGeom4326
     *
     * @return DirRegion
     */
    public function setTheGeom4326($theGeom4326 = null)
    {
        $this->theGeom4326 = $theGeom4326;

        return $this;
    }

    /**
     * Get theGeom4326.
     *
     * @return geometry|null
     */
    public function getTheGeom4326()
    {
        return $this->theGeom4326;
    }

    /**
     * Set theGeom900913.
     *
     * @param geometry|null $theGeom900913
     *
     * @return DirRegion
     */
    public function setTheGeom900913($theGeom900913 = null)
    {
        $this->theGeom900913 = $theGeom900913;

        return $this;
    }

    /**
     * Get theGeom900913.
     *
     * @return geometry|null
     */
    public function getTheGeom900913()
    {
        return $this->theGeom900913;
    }

    /**
     * Set koatuuT.
     *
     * @param string|null $koatuuT
     *
     * @return DirRegion
     */
    public function setKoatuuT($koatuuT = null)
    {
        $this->koatuuT = $koatuuT;

        return $this;
    }

    /**
     * Get koatuuT.
     *
     * @return string|null
     */
    public function getKoatuuT()
    {
        return $this->koatuuT;
    }

    /**
     * Set xMin.
     *
     * @param float|null $xMin
     *
     * @return DirRegion
     */
    public function setXMin($xMin = null)
    {
        $this->xMin = $xMin;

        return $this;
    }

    /**
     * Get xMin.
     *
     * @return float|null
     */
    public function getXMin()
    {
        return $this->xMin;
    }

    /**
     * Set yMin.
     *
     * @param float|null $yMin
     *
     * @return DirRegion
     */
    public function setYMin($yMin = null)
    {
        $this->yMin = $yMin;

        return $this;
    }

    /**
     * Get yMin.
     *
     * @return float|null
     */
    public function getYMin()
    {
        return $this->yMin;
    }

    /**
     * Set xMax.
     *
     * @param float|null $xMax
     *
     * @return DirRegion
     */
    public function setXMax($xMax = null)
    {
        $this->xMax = $xMax;

        return $this;
    }

    /**
     * Get xMax.
     *
     * @return float|null
     */
    public function getXMax()
    {
        return $this->xMax;
    }

    /**
     * Set yMax.
     *
     * @param float|null $yMax
     *
     * @return DirRegion
     */
    public function setYMax($yMax = null)
    {
        $this->yMax = $yMax;

        return $this;
    }

    /**
     * Get yMax.
     *
     * @return float|null
     */
    public function getYMax()
    {
        return $this->yMax;
    }

    /**
     * Set info.
     *
     * @param string|null $info
     *
     * @return DirRegion
     */
    public function setInfo($info = null)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info.
     *
     * @return string|null
     */
    public function getInfo()
    {
        return $this->info;
    }
}

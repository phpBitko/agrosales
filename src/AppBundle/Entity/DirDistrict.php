<?php
/**
 * Created by PhpStorm.
 * User: vetal
 * Date: 10.07.2018
 * Time: 15:51
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @ORM\Table(name="dir_district")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DirDistrictRepository")
 */
class DirDistrict
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="dir_district_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

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
     * @var string
     *
     * @ORM\Column(name="natoray", type="string", length=30, nullable=true)
     */
    private $natoray;

    /**
     * @var string
     *
     * @ORM\Column(name="vpor", type="string", length=100, nullable=true)
     */
    private $vpor;

    /**
     * @var string
     *
     * @ORM\Column(name="vpos", type="string", length=100, nullable=true)
     */
    private $vpos;

    /**
     * @var string
     *
     * @ORM\Column(name="mpd", type="string", length=50, nullable=true)
     */
    private $mpd;

    /**
     * @var string
     *
     * @ORM\Column(name="datep", type="string", length=10, nullable=true)
     */
    private $datep;

    /**
     * @var float
     *
     * @ORM\Column(name="patoray", type="float", precision=10, scale=0, nullable=true)
     */
    private $patoray;

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
     * @var integer
     *
     * @ORM\Column(name="gid", type="integer", nullable=true)
     */
    private $gid;

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
     * @var integer
     *
     * @ORM\Column(name="id_office", type="integer", nullable=true)
     */
    private $idOffice;

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
    public function getKoatuu()
    {
        return $this->koatuu;
    }

    /**
     * @param string $koatuu
     */
    public function setKoatuu($koatuu)
    {
        $this->koatuu = $koatuu;
    }

    /**
     * @return string
     */
    public function getNatoobl()
    {
        return $this->natoobl;
    }

    /**
     * @param string $natoobl
     */
    public function setNatoobl($natoobl)
    {
        $this->natoobl = $natoobl;
    }

    /**
     * @return string
     */
    public function getNatoray()
    {
        return $this->natoray;
    }

    /**
     * @param string $natoray
     */
    public function setNatoray($natoray)
    {
        $this->natoray = $natoray;
    }

    /**
     * @return string
     */
    public function getVpor()
    {
        return $this->vpor;
    }

    /**
     * @param string $vpor
     */
    public function setVpor($vpor)
    {
        $this->vpor = $vpor;
    }

    /**
     * @return string
     */
    public function getVpos()
    {
        return $this->vpos;
    }

    /**
     * @param string $vpos
     */
    public function setVpos($vpos)
    {
        $this->vpos = $vpos;
    }

    /**
     * @return string
     */
    public function getMpd()
    {
        return $this->mpd;
    }

    /**
     * @param string $mpd
     */
    public function setMpd($mpd)
    {
        $this->mpd = $mpd;
    }

    /**
     * @return string
     */
    public function getDatep()
    {
        return $this->datep;
    }

    /**
     * @param string $datep
     */
    public function setDatep($datep)
    {
        $this->datep = $datep;
    }

    /**
     * @return float
     */
    public function getPatoray()
    {
        return $this->patoray;
    }

    /**
     * @param float $patoray
     */
    public function setPatoray($patoray)
    {
        $this->patoray = $patoray;
    }

    /**
     * @return geometry
     */
    public function getTheGeom()
    {
        return $this->theGeom;
    }

    /**
     * @param geometry $theGeom
     */
    public function setTheGeom($theGeom)
    {
        $this->theGeom = $theGeom;
    }

    /**
     * @return int
     */
    public function getCoordsysGeom()
    {
        return $this->coordsysGeom;
    }

    /**
     * @param int $coordsysGeom
     */
    public function setCoordsysGeom($coordsysGeom)
    {
        $this->coordsysGeom = $coordsysGeom;
    }

    /**
     * @return geometry
     */
    public function getTheGeom42Bl()
    {
        return $this->theGeom42Bl;
    }

    /**
     * @param geometry $theGeom42Bl
     */
    public function setTheGeom42Bl($theGeom42Bl)
    {
        $this->theGeom42Bl = $theGeom42Bl;
    }

    /**
     * @return int
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * @param int $gid
     */
    public function setGid($gid)
    {
        $this->gid = $gid;
    }

    /**
     * @return geometry
     */
    public function getTheGeom4326()
    {
        return $this->theGeom4326;
    }

    /**
     * @param geometry $theGeom4326
     */
    public function setTheGeom4326($theGeom4326)
    {
        $this->theGeom4326 = $theGeom4326;
    }

    /**
     * @return geometry
     */
    public function getTheGeom900913()
    {
        return $this->theGeom900913;
    }

    /**
     * @param geometry $theGeom900913
     */
    public function setTheGeom900913($theGeom900913)
    {
        $this->theGeom900913 = $theGeom900913;
    }

    /**
     * @return string
     */
    public function getKoatuuT()
    {
        return $this->koatuuT;
    }

    /**
     * @param string $koatuuT
     */
    public function setKoatuuT($koatuuT)
    {
        $this->koatuuT = $koatuuT;
    }

    /**
     * @return float
     */
    public function getXMin()
    {
        return $this->xMin;
    }

    /**
     * @param float $xMin
     */
    public function setXMin($xMin)
    {
        $this->xMin = $xMin;
    }

    /**
     * @return float
     */
    public function getYMin()
    {
        return $this->yMin;
    }

    /**
     * @param float $yMin
     */
    public function setYMin($yMin)
    {
        $this->yMin = $yMin;
    }

    /**
     * @return float
     */
    public function getXMax()
    {
        return $this->xMax;
    }

    /**
     * @param float $xMax
     */
    public function setXMax($xMax)
    {
        $this->xMax = $xMax;
    }

    /**
     * @return float
     */
    public function getYMax()
    {
        return $this->yMax;
    }

    /**
     * @param float $yMax
     */
    public function setYMax($yMax)
    {
        $this->yMax = $yMax;
    }

    /**
     * @return int
     */
    public function getIdOffice()
    {
        return $this->idOffice;
    }

    /**
     * @param int $idOffice
     */
    public function setIdOffice($idOffice)
    {
        $this->idOffice = $idOffice;
    }



}

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
}
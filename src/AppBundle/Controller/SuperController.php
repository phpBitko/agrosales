<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Advertisement;
use AppBundle\Controller\ControllerTrait\GeneralFunction;
use AppBundle\Entity\Interfaces\InstanceUserInterface;
/**
 * Class SuperController
 *
 */
class SuperController extends Controller
{
    use GeneralFunction;

    public const RESPONSE_STATUS_OK = 0;
    public const RESPONSE_STATUS_WARNING = 1;
    public const RESPONSE_STATUS_EXCEPTION = 2;

    public const STATUS_ADVERTISEMENT = [
        'ACTIVE' => 1,
        'PENDING' => 2,
        'REJECT' => 3,
        'DEACTIVATED' => 4
    ];


    protected $errors = '';

    /**
     * @var EntityManagerInterface
     */
    protected $em;



    /**
     * SuperController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * Генерує тип сортування
     *
     * @param Request $request
     * @return array
     */
    protected function getOrder(Request $request)
    {
        $sortField = $request->query->get('sort');
        $sortDirection = $request->query->get('direction');

        if(!empty($sortField)) {
            $order = [substr($sortField, 2) => $sortDirection];
        } else {
            $order = Advertisement::$order;
        }

        return $order;

    }

}
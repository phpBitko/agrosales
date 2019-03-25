<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class StatisticData extends BaseEmServices
{
    public function getDataForSidebar(){
        $em = $this->getDoctrine()->getManager();

        $countAdvertisement = $em->getRepository('AppBundle:Advertisement')->getCountAdvertisementByStatus($this->getUser()->getId());

        $data['countAdvertisement'] = [];
        $countAll = 0;
        if (is_array($countAdvertisement)) {
            foreach ($countAdvertisement as $k => $v) {
                $data['countAdvertisement'][$v['id']] = $v;
                $countAll += $v['count'];
            }
        }
        $data['countAdvertisement']['countAll'] = $countAll;

        $countNotViewMessages = $em->getRepository('AppBundle:Messages')->getCountNotViewMessages($this->getUser()->getId());
        $data['countNotViewMessages'] = [];
        if (is_array($countNotViewMessages)) {
            foreach ($countNotViewMessages as $k => $v) {
                $data['countNotViewMessages'][$v['status']] = $v;
            }
        }

        return $data;
    }

    public function getCountAdvertisement($userId = 0)
    {
        $countAdvertisementByStatus = $this->entityManager
            ->getRepository('AppBundle:Advertisement')
            ->getCountAdvertisementByStatus($userId);

        $total = 0;
        if (is_array($countAdvertisementByStatus)) {
            foreach ($countAdvertisementByStatus as $k => $v) {
                $total += $v['count'];
            }
        }
        $countAdvertisementByStatus['totalAdvertisement'] = $total;

        return  $countAdvertisementByStatus;
    }




}
<?php

namespace AppBundle\Controller\ControllerTrait;

use AppBundle\Entity\Advertisement;
use AppBundle\Entity\Interfaces\InstanceUserInterface;

trait GeneralFunction{

    protected function setStatusAdvertisement(Advertisement $advertisement, int $status)
    {
        $em = $this->getDoctrine()->getManager();
        $advertisement->setDirStatus($em->getRepository('AppBundle:DirStatus')->find($status));
        $em->persist($advertisement);
        $em->flush();
    }

    protected function checkUserWithAuthorException(InstanceUserInterface $object)
    {
        if (!$this->checkUserWithAuthorBool($object)) {
            throw $this->createAccessDeniedException();
        }
    }

    protected function checkUserWithAuthorBool(InstanceUserInterface $object)
    {
        if ($object === null) {
            throw $this->createNotFoundException();
        }

        if ($object->getUsers() === $this->getUser()) {
            return true;
        }
        return false;
    }
}
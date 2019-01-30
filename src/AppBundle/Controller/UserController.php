<?php
namespace AppBundle\Controller;


use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * Class CabinetController
 * @Route("/user")
 */
class UserController extends SuperController{


    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/enabledUser/{token}", name="user_enabled_user", methods={"GET"})
     */
    public function enabledUser($token){

        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('AppBundle:User');
        $user = $userRepository->findOneBy(['mailToken'=>$token]);

        if(!empty($user)){
            $user->setEnabled(true);
            $status = true;
            $em->persist($user);
            $em->flush();
        }else{
            $status = false;
        }

        return $this->render('AppBundle:user:enabled_user.html.twig', array(
            'status' => $status,
        ));
    }

}

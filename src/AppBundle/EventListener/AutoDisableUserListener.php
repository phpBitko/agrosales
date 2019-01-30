<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;

class AutoDisableUserListener implements EventSubscriberInterface {

    private $session;
    private $router;
    private $entityManager;
    private $mailer;
    private $email;

    public function __construct(SessionInterface $session, UrlGeneratorInterface $router, EntityManagerInterface $entityManager, \Swift_Mailer $mailer, string $mailerUser) {
        $this->session = $session;
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->email = $mailerUser;
    }

    public static function getSubscribedEvents() {
        return [FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess'];
    }

    public function onRegistrationSuccess(FormEvent $event) {
        $user = $event->getForm()->getData();
        $user->setEnabled(false);
        $mailToken = substr(bin2hex(random_bytes(32)), 0 , 32);
        $user->setMailToken($mailToken);

        $message = \Swift_Message::newInstance();
        $message->setSubject('Команда LandSales вітає з реєстрацією '. $user->getUsername().'!')
            ->setFrom($this->email)
            ->setTo($user->getEmail())
            ->setBody(
                'Для завершення реєстрації перейдіть за посиланням ' . $this->router->generate('user_enabled_user', ['token' => $mailToken], $this->router::ABSOLUTE_URL )
            );

        $this->mailer->send($message);

        $this->session->getFlashBag()->add('notice', 'Дякуємо за реєстрацію. Для підтвердження акаунта перейдіть по посиланню, яке прийде на Вашу електронну адресу.');

        $url = $this->router->generate('fos_user_security_login');
        $event->setResponse(new RedirectResponse($url));
    }
}

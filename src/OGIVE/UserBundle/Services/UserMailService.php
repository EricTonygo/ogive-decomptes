<?php

namespace OGIVE\UserBundle\Services;

/**
 * Description of UserMailService
 *
 * @author Eric TONYE
 */
class UserMailService {

    protected $mailer;
    protected $templating;

    public function __construct(\Swift_Mailer $mailer, \Symfony\Bundle\TwigBundle\TwigEngine $templating) {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendAccountActivationLink(\OGIVE\UserBundle\Entity\User $user) {
        if ($user && $user->getEmail() != "") {
            $message = \Swift_Message::newInstance()
                    ->setSubject("Account activation")
                    ->setFrom(array('infos@siogive.com' => "OGIVE DECOMPTE"))
                    ->setTo($user->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEUserBundle:send-mail:template-activation-account.html.twig', array('user' => $user)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }

}

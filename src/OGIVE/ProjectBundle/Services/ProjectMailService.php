<?php

namespace OGIVE\ProjectBundle\Services;

use OGIVE\ProjectBundle\Entity\Decompte;
use OGIVE\ProjectBundle\Entity\Contributor;
/**
 * Description of ProjectMailService
 *
 * @author Eric TONYE
 */
class ProjectMailService {

    protected $mailer;
    protected $templating;

    public function __construct(\Swift_Mailer $mailer, \Symfony\Bundle\TwigBundle\TwigEngine $templating) {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }
    
    public function sendSubmittedDecompteLink(Decompte $submittedDecompte, Contributor $holder, Contributor $recipientContributor) {
        if (!is_null($submittedDecompte) && !is_null($holder) && !is_null($holder->getEmail()) && !is_null($recipientContributor) && !is_null($recipientContributor->getEmail())) {
            $message = \Swift_Message::newInstance()
                    ->setSubject("Soumission du décompte mensuel de : ".$submittedDecompte->getMonthName())
                    ->setFrom(array('infos@siogive.com' => "OGIVE DECOMPTE"))
                    ->setTo($recipientContributor->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEProjectBundle:send-mail:template-submitting-decompte.html.twig', array('holder' => $holder, "submittedDecompte" => $submittedDecompte, "recipientContributor" => $recipientContributor)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }

}

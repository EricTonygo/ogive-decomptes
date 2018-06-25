<?php

namespace OGIVE\ProjectBundle\Services;

use OGIVE\ProjectBundle\Entity\Decompte;
use OGIVE\ProjectBundle\Entity\DecompteValidation;
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
                    ->setFrom(array('infos@siogive.com' => "Follow Up Contracts"))
                    ->setTo($recipientContributor->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEProjectBundle:send-mail:template-submitting-decompte.html.twig', array('holder' => $holder, "submittedDecompte" => $submittedDecompte, "recipientContributor" => $recipientContributor)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }
    
    public function sendNotificationForDecompteValidation(Decompte $submittedDecompte, DecompteValidation $senderValidation, DecompteValidation $recipientValidation) {
        if (!is_null($submittedDecompte) && !is_null($senderValidation) && !is_null($senderValidation->getUser())) {
            $message = \Swift_Message::newInstance()
                    ->setSubject($senderValidation->getUser()->getLastname()." a validé le decompte du mois de : ".$submittedDecompte->getMonthName())
                    ->setFrom(array('infos@siogive.com' => "Follow Up Contracts"))
                    ->setTo($recipientValidation->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEProjectBundle:send-mail:template-notification-validation.html.twig', array('senderValidation' => $senderValidation, 'recipientValidation' => $recipientValidation, "submittedDecompte" => $submittedDecompte)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }
    
    public function sendNotificationForSenderDecompteValidation(Decompte $submittedDecompte, DecompteValidation $senderValidation) {
        if (!is_null($submittedDecompte) && !is_null($senderValidation) && !is_null($senderValidation->getUser())) {
            $message = \Swift_Message::newInstance()
                    ->setSubject("Votre validation le decompte du mois de : ".$submittedDecompte->getMonthName())
                    ->setFrom(array('infos@siogive.com' => "Follow Up Contracts"))
                    ->setTo($senderValidation->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEProjectBundle:send-mail:template-notification-sender-validation.html.twig', array('senderValidation' => $senderValidation, "submittedDecompte" => $submittedDecompte)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }

}

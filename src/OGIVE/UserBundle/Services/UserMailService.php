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

    public function sendAccountActivationLink(\OGIVE\UserBundle\Entity\User $user = null) {
        if ($user && $user->getEmail() != "") {
            $message = \Swift_Message::newInstance()
                    ->setSubject("Account activation")
                    ->setFrom(array('infos@siogive.com' => "Follow Up Contracts"))
                    ->setTo($user->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEUserBundle:send-mail:template-activation-account.html.twig', array('user' => $user)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }
    
    public function sendWelcomeNewUserEmail(\OGIVE\UserBundle\Entity\User $user = null) {
        if ($user && $user->getEmail() != "") {
            $message = \Swift_Message::newInstance()
                    ->setSubject("Bienvenu à Follow Up Contract")
                    ->setFrom(array('infos@siogive.com' => "Follow Up Contracts"))
                    ->setTo($user->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEUserBundle:send-mail:template-welcome-new-user.html.twig', array('user' => $user)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }
    
    public function sendNotificationToOwnerRegistration(\OGIVE\ProjectBundle\Entity\Project $project) {
        if ($project && $project->getOwner()->getUser()->getEmail() != "") {
            $subject = "Mr ".$project->getOwner()->getUser()->getLastname().", vous avez été désigné maitre d'ouvrage du marché N°: ".$project->getNumeroMarche();
            $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom(array('infos@siogive.com' => "Follow Up Contracts"))
                    ->setTo($project->getOwner()->getUser()->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEUserBundle:send-mail:template-owner-registration.html.twig', array('project' => $project)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }

    public function sendNotificationToProjectManagerRegistration(\OGIVE\ProjectBundle\Entity\ProjectManager $projectManager) {
        if ($projectManager && $projectManager->getUser()->getEmail() != "") {
            $subject = "Mr ".$projectManager->getUser()->getLastname().", vous avez été désigné maitre d'oeuvre du marché N°: ".$projectManager->getProject()->getNumeroMarche();
            $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom(array('infos@siogive.com' => "Follow Up Contracts"))
                    ->setTo($projectManager->getUser()->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEUserBundle:send-mail:template-project-manager-registration.html.twig', array('projectManager' => $projectManager)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }
    
    public function sendNotificationToHolderRegistration(\OGIVE\ProjectBundle\Entity\Holder $holder) {
        if ($holder->getUser() && $holder->getUser()->getEmail() != "") {
            $subject = "Mr ".$holder->getUser()->getLastname().", vous avez été désigné titulaire du marché N°: ".$holder->getProject()->getNumeroMarche();
            $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom(array('infos@siogive.com' => "Follow Up Contracts"))
                    ->setTo($holder->getUser()->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEUserBundle:send-mail:template-holder-registration.html.twig', array('holder' => $holder)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }
    
    public function sendNotificationToOtherContributorRegistration(\OGIVE\ProjectBundle\Entity\OtherContributor $otherContributor) {
        if ($otherContributor && $otherContributor->getUser()->getEmail() != "") {
            $subject = "Mr ".$otherContributor->getUser()->getLastname().", vous avez été désigné titulaire du marché N°: ".$otherContributor->getProject()->getNumeroMarche();
            $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom(array('infos@siogive.com' => "Follow Up Contracts"))
                    ->setTo($otherContributor->getUser()->getEmail())
                    ->setBody(
                    $this->templating->render('OGIVEUserBundle:send-mail:template-otherContributor-registration.html.twig', array('otherContributor' => $otherContributor)), 'text/html'
            );
            $this->mailer->send($message);
        } else {
            return true;
        }
    }
    
    public function sendNotificationToContributors(\OGIVE\ProjectBundle\Entity\Project $project){
        $this->sendNotificationToOwnerRegistration($project);
        $projectManagers = $project->getProjectManagers();
        foreach ($projectManagers as $projectManager) {
            $this->sendNotificationToProjectManagerRegistration($projectManager);
        }
        $holders = $project->getHolders();
        foreach ($holders as $holder) {
            $this->sendNotificationToHolderRegistration($holder);
        }
        $othersContributors = $project->getOtherContributors();
        foreach ($othersContributors as $otherContributor) {
            $this->sendNotificationToOtherContributorRegistration($otherContributor);
        }
    }
}

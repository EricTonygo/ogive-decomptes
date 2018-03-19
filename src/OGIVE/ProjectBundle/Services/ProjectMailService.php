<?php

namespace OGIVE\ProjectBundle\Services;

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

}

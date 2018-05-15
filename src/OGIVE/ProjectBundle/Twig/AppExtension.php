<?php

namespace OGIVE\ProjectBundle\Twig;

/**
 * Description of AppExtension
 *
 * @author Eric TONYE
 */
class AppExtension extends \Twig_Extension {

    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('country_name', function($value) {
                        return \Symfony\Component\Intl\Intl::getRegionBundle()->getCountryName($value);
                    }),
            
            new \Twig_SimpleFilter('projectStatus', function($value) {
                        switch ($value) {
                            case 1:
                                return "En cours";
                            case 2:
                                return "Suspendu";
                            case 3:
                                return "Cloturé";
                            default:
                                return "";
                        }
                    }),
        );
    }

    public function getName() {
        return 'app_extension';
    }

}

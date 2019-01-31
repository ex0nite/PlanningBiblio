<?php

namespace PlanningBiblio;

class LegacyCodeChecker
{
    private $twigized = array(
        'absences/infos.php',
        'statistiques/presents_absents.php',
    );

    public function isTwigized($page)
    {
        if (in_array($page, $this->twigized)) {
            return true;
        }

        return false;
    }
}

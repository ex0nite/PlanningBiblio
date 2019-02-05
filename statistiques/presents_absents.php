<?php
/**
  Planning Biblio
  Licence GNU/GPL (version 2 et au dela)
  See README.md and LICENSE files
  @copyright 2011-2018 Jérôme Combes

  Fichier : statistiques/presents_absents.php
  created : 2019-01-30
  Last changes : 2019-01-30
  @author Alex Arnaud <alex.arnaud@biblibre.com>

  Description :
  Show present and missing staff member for a given day.
*/

require_once __DIR__ . "/../absences/class.absences.php";
include_once __DIR__ . "/../include/function.php";
include_once __DIR__ . "/../include/db.php";

use PlanningBiblio\PresentSet;

$params = $request->request->all();
if ($params) {
    $startTime = strtotime(dateSQL($params['from']));
    $endTime = strtotime(dateSQL($params['to']));
    $validated = isset($params['validated']) && $params['validated'] == 'on' ? true : false;

    $by_date = array();
    for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
        $date = date('Y-m-d', $i);

        $absences = new absences();
        $absences->valide = false;
        $absent_ids = array(2);
        $absences->valide = $validated;
        $absences->fetch("`nom`,`prenom`,`debut`,`fin`", null, $date, $date, array(1));
        $absents = $absences->elements;

        foreach ($absents as $key => $absent) {
            $absents[$key]['motif'] = html_entity_decode($absent['motif'], ENT_QUOTES|ENT_HTML5);
            preg_match('/00:00:00$/', $absent['debut'], $matche_start, PREG_OFFSET_CAPTURE);
            preg_match('/23:59:59$/', $absent['fin'], $matche_end, PREG_OFFSET_CAPTURE);
            if ($matche_start && $matche_end) {
                $absents[$key]['all_the_day'] = 1;
            } else {
                $absents[$key]['from'] = substr($absent['debutAff'], -5);
                $absents[$key]['to'] = substr($absent['finAff'], -5);
            }

            if ($absent['debut'] <= $date . " 00:00:00"
                and $absent['fin'] >= $date . " 23:59:59"
                and $absent['valide'] > 0) {
                $absent_ids[] = $absent['perso_id'];
            }
        }

        $d = new datePL($date);
        $presentset = new PresentSet($date, $d, $absent_ids, new db());
        $presents = $presentset->all();
        foreach ($presents as $key => $present) {
            $presents[$key]['heures'] = html_entity_decode($present['heures'], ENT_QUOTES|ENT_HTML5);
        }

        $by_date[] = array(
            'date' => date('d/m/Y', $i),
            'presents' => $presents,
            'absents' => $absents
        );
    }

    $templates_params['by_date'] = $by_date;
    $templates_params['from'] = $params['from'];
    $templates_params['to'] = $params['to'];
    $templates_params['validated'] = $validated;
}


$template = $twig->load('statistiques/presents_absents.html.twig');
echo $template->render($templates_params);
exit;
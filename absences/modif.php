<?php
/**
Planning Biblio, Version 2.8
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
@copyright 2011-2018 Jérôme Combes

Fichier : absences/modif.php
Création : mai 2011
Dernière modification : 30 avril 2018
@author Jérôme Combes <jerome@planningbiblio.fr>
@author Farid Goara <farid.goara@u-pem.fr>

Description :
Formulaire permettant de modifier

Page appelée par la page index.php
*/

require_once "class.absences.php";
require_once "motifs.php";

//	Initialisation des variables
$id=filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

$display=null;
$checked=null;

// Gestion des droits d'accès
// 20x : gestion niveau 1
// 50x : gestion niveau 2

$adminN1 = false;
$adminN2 = false;

// Si droit de gestion des absences N1 ou N2 sur l'un des sites : accès à cette page autorisé
// Les droits d'administration des absences seront ajustés ensuite
for ($i = 1; $i <= $config['Multisites-nombre']; $i++) {
    if (in_array((200+$i), $droits)) {
        $adminN1 = true;
    }
    if (in_array((500+$i), $droits)) {
        $adminN2 = true;
    }
}

$agents_multiples = ($adminN1 or $adminN2 or in_array(9, $droits));

$a=new absences();
$a->fetchById($id);

$agents=$a->elements['agents'];
$groupe=$a->elements['groupe'];
$perso_id=$a->elements['perso_id'];
$perso_ids=$a->elements['perso_ids'];
$nom=$a->elements['nom'];
$prenom=$a->elements['prenom'];
$motif=$a->elements['motif'];
$motif_autre=$a->elements['motif_autre'];
$commentaires=$a->elements['commentaires'];
$demande=filter_var($a->elements['demande'], FILTER_CALLBACK, array("options"=>"sanitize_dateTimeSQL"));
$debutSQL=filter_var($a->elements['debut'], FILTER_CALLBACK, array("options"=>"sanitize_dateTimeSQL"));
$finSQL=filter_var($a->elements['fin'], FILTER_CALLBACK, array("options"=>"sanitize_dateTimeSQL"));
$valide=filter_var($a->elements['valide_n2'], FILTER_SANITIZE_NUMBER_INT);
$validation=$a->elements['validation_n2'];
$valideN1=$a->elements['valide_n1'];
$validationN1=$a->elements['validation_n1'];
$ical_key=$a->elements['ical_key'];
$cal_name=$a->elements['cal_name'];
$rrule=$a->elements['rrule'];

// Pièces justificatives
$pj1Checked=$a->elements['pj1']?"checked='checked'":null;
$pj2Checked=$a->elements['pj2']?"checked='checked'":null;
$soChecked=$a->elements['so']?"checked='checked'":null;

// Traitement des dates et des heures
$demande=dateFr($demande, true);
$debut=dateFr3($debutSQL);
$fin=dateFr3($finSQL);

$hre_debut=substr($debut, -8);
$hre_fin=substr($fin, -8);
$debut=substr($debut, 0, 10);
$fin=substr($fin, 0, 10);

if ($hre_debut=="00:00:00" and $hre_fin=="23:59:59") {
    $checked="checked='checked'";
    $display="style='display:none;'";
}

// Initialisation des menus déroulants
$select1=$valide==0?"selected='selected'":null;
$select2=$valide>0?"selected='selected'":null;
$select3=$valide<0?"selected='selected'":null;
$select4=null;
$select5=null;
if ($valide==0 and $valideN1!=0) {
    $select4=$valideN1>0?"selected='selected'":null;
    $select5=$valideN1<0?"selected='selected'":null;
}
$validation_texte=$valide>0?"Valid&eacute;e":"&nbsp;";
$validation_texte=$valide<0?"Refus&eacute;e":$validation_texte;
$validation_texte=$valide==0?"Demand&eacute;e":$validation_texte;
if ($valide==0 and $valideN1!=0) {
    $validation_texte=$valideN1>0?"Valid&eacute;e (en attente de validation hi&eacute;rarchique)":$validation_texte;
    $validation_texte=$valideN1<0?"Refus&eacute;e (en attente de validation hi&eacute;rarchique)":$validation_texte;
}

$display_autre=in_array(strtolower($motif), array("autre","other"))?null:"style='display:none;'";


// Si le motif enregistré pour l'absence n'existe pas dans la liste des motifs, on l'ajoute à cette liste pour l'affichage du select
$patternExists=false;
foreach ($motifs as $elem) {
    if ($elem['valeur'] == $motif) {
        $patternExists=true;
        break;
    }
}
if (!$patternExists) {
    $motifs[]=array("id"=>"99999", "valeur"=>$motif, "type"=> "0");
}

// Si l'option "Absences-notifications-agent-par-agent" est cochée, adapte la variable $adminN1 en fonction des agents de l'absence. S'ils sont tous gérés, $adminN1 = true, sinon, $adminN1 = false
if ($config['Absences-notifications-agent-par-agent'] and $adminN1) {
    $perso_ids_verif = array(0);

    $db = new db();
    $db->select2('responsables', 'perso_id', array('responsable' => $_SESSION['login_id']));
    if ($db->result) {
        foreach ($db->result as $elem) {
            $perso_ids_verif[] = $elem['perso_id'];
        }
    }

    foreach ($agents as $elem) {
        if (!in_array($elem['perso_id'], $perso_ids_verif)) {
            $adminN1 = false;
            break;
        }
    }
}

// Sécurité
// Droit 6 = modification de ses propres absences
// Les admins ont toujours accès à cette page
$acces = ($adminN1 or $adminN2);
if (!$acces) {
    // Les non admin ayant le droits de modifier leurs absences ont accès si l'absence les concerne
    $acces=(in_array(6, $droits) and $perso_id==$_SESSION['login_id'])?true:false;
}
// Si config Absences-adminSeulement, seuls les admins ont accès à cette page
if ($config['Absences-adminSeulement'] and !($adminN1 or $adminN2)) {
    $acces=false;
}
if (!$acces) {
    echo "<h3>Modification de l'absence</h3>\n";
    echo "Vous n'êtes pas autorisé(e) à modifier cette absence.<br/><br/>\n";
    echo "<a href='index.php?page=absences/voir.php'>Retour à la liste des absences</a><br/><br/>\n";
    include "include/footer.php";
    exit;
}

// Multisites, ne pas afficher les absences des agents d'un site non géré
if ($config['Multisites-nombre']>1) {
    // $sites_agents comprend l'ensemble des sites en lien avec les agents concernés par cette modification d'absence
    $sites_agents=array();
    foreach ($agents as $elem) {
        if (is_array($elem['sites'])) {
            foreach ($elem['sites'] as $site) {
                if (!in_array($site, $sites_agents)) {
                    $sites_agents[]=$site;
                }
            }
        }
    }

    if (!$config['Absences-notifications-agent-par-agent']) {
        $adminN1 = false;
    }
    $adminN2 = false;

    foreach ($sites_agents as $site) {
        if (!$config['Absences-notifications-agent-par-agent']) {
            if (in_array((200+$site), $droits)) {
                $adminN1 = true;
            }
        }
        if (in_array((500+$site), $droits)) {
            $adminN2 = true;
        }
    }

    if (!($adminN1 or $adminN2) and !$acces) {
        echo "<h3>Modification de l'absence</h3>\n";
        echo "Vous n'êtes pas autorisé(e) à modifier cette absence.<br/><br/>\n";
        echo "<a href='index.php?page=absences/voir.php'>Retour à la liste des absences</a><br/><br/>\n";
        include "include/footer.php";
        exit;
    }
}

$admin = $adminN1 or $adminN2;

// Si l'absence est importée depuis un agenda extérieur, on interdit la modification
$agenda_externe = false;
if ($ical_key and substr($cal_name, 0, 14) != 'PlanningBiblio') {
    $agenda_externe = true;
    $admin=false;
}

// Liste des agents
if ($agents_multiples) {
    $db_perso=new db();
    $db_perso->select2("personnel", "*", array("supprime"=>0,"id"=>"<>2"), "order by nom,prenom");
    $agents_tous=$db_perso->result?$db_perso->result:array();
}

echo "<h3>Modification de l'absence</h3>\n";
echo "<form name='form' id='form' method='get' action='index.php' onsubmit='return verif_absences(\"debut=date1;fin=date2;motif\");'>\n";
echo "<input type='hidden' name='CSRFToken' value='$CSRFSession' />\n";
echo "<input type='hidden' name='page' value='absences/modif2.php' />\n";
echo "<input type='hidden' name='perso_id' value='$perso_id' />\n";		// nécessaire pour verif_absences
echo "<input type='hidden' id='admin' value='".($admin?1:0)."' />\n";
echo "<input type='hidden' id='login_id' value='{$_SESSION['login_id']}' />\n";
echo "<input type='hidden' name='groupe' id='groupe' value='$groupe' />\n";
echo "<input type='hidden' name='rrule' id='rrule' value='$rrule' />\n";
echo "<input type='hidden' name='recurrence-modif' id='recurrence-modif' value='' />\n";
echo "<table class='tableauFiches'>\n";


// Liste des agents absents (champs input[hidden])
// Utile qu'on soit admin ou non, qu'il y est un absent ou plusieurs
foreach ($agents as $elem) {
    echo "<input type='hidden' name='perso_ids[]' value='{$elem['perso_id']}' id='hidden{$elem['perso_id']}' class='perso_ids_hidden'/>\n";
}


// Si admin, affiche les agents de l'absence et offre la possibilité d'en ajouter
if ($agents_multiples) {
    echo "<tr><td><label class='intitule'>Agent(s)</label></td><td colspan='2'>";
  
    // Liste des agents absents / Affichage de la liste (chargée en JQuery)
    echo "<ul id='perso_ul1' class='perso_ul'></ul>\n";
    echo "<ul id='perso_ul2' class='perso_ul'></ul>\n";
    echo "<ul id='perso_ul3' class='perso_ul'></ul>\n";
    echo "<ul id='perso_ul4' class='perso_ul'></ul>\n";
    echo "<ul id='perso_ul5' class='perso_ul'></ul>\n";

    echo "</td></tr>\n";
    echo "<tr><td>&nbsp;</td><td>\n";

    // Menu déroulant
    echo "<select name='perso_id' id='perso_ids' class='ui-widget-content ui-corner-all' style='margin-bottom:20px;'>\n";
    echo "<option value='0' selected='selected'>-- Ajoutez un agent --</option>\n";
    if ($config['Absences-tous']) {
        echo "<option value='tous'>Tous les agents</option>\n";
    }

    foreach ($agents_tous as $elem) {
        $hidden=in_array($elem['id'], $perso_ids)?"style='display:none;'":null;
        echo "<option value='".$elem['id']."' id='option{$elem['id']}' $hidden>".$elem['nom']." ".$elem['prenom']."</option>\n";
    }
    echo "</select>\n";

    echo "</td></tr>\n";

// Si pas admin : affiche l'agent ou la liste des agents de l'absences sans possibilité d'ajouter / supprimer
} else {
    if (count($agents)>1) {
        echo "<tr><td><label class='intitule'>Agents</label></td><td>";

        // Liste des agents affichés
        echo "<ul>\n";
        foreach ($agents as $elem) {
            echo "<li>{$elem['nom']}&nbsp;{$elem['prenom']}</li>\n";
        }
        echo "</ul>\n";
        echo "</td></tr>\n";
    } else {
        echo "<tr><td><label class='intitule'>Agent</label></td><td>";
        echo $nom;
        echo "&nbsp;";
        echo $prenom;
        echo "</td></tr>\n";
    }
}
echo "<tr><td>\n";
echo "<label class='intitule'>Journée(s) entière(s)</label>\n";
echo "</td><td>\n";
echo "<input type='checkbox' name='allday' $checked onclick='all_day();'/>\n";
echo "</td></tr>\n";
echo "<tr><td>";
echo "<label class='intitule'>Date de début</label></td><td style='white-space:nowrap;'>";
echo "<input type='text' name='debut' value='$debut' style='width:100%;' class='datepicker'/>\n";
echo "</td></tr>\n";
echo "<tr id='hre_debut' $display ><td>\n";
echo "<label class='intitule'>Heure de début</label>\n";
echo "</td><td>\n";
echo "<select name='hre_debut' class='center ui-widget-content ui-corner-all'>\n";
selectHeure(7, 23, true, $hre_debut);
echo "</select>\n";
echo "</td></tr>\n";

echo "<tr><td>";
echo "<label class='intitule'>Date de fin</label></td><td style='white-space:nowrap;'>";
echo "<input type='text' name='fin' value='$fin' style='width:100%;' class='datepicker'/>\n";
echo "</td></tr>\n";
echo "<tr id='hre_fin' $display ><td>\n";
echo "<label class='intitule'>Heure de fin</label>\n";
echo "</td><td>\n";
echo "<select name='hre_fin' class='center ui-widget-content ui-corner-all' onfocus='setEndHour();'>\n";
selectHeure(7, 23, true, $hre_fin);
echo "</select>\n";
echo "</td></tr>\n";

echo "<tr><td style='padding-bottom:30px;'>\n";
echo "<label class='intitule'>Récurrence</label>\n";
echo "</td><td style='padding-bottom:30px;'>\n";
echo "<input type='checkbox' name='recurrence-checkbox' id='recurrence-checkbox' value='1' disabled='disabled' />\n";
echo "<span id='recurrence-info' style='display:none;'><span id='recurrence-summary'>&nbsp;</span><a href='#' id='recurrence-link' style='margin-left:10px; display:none;'>Modifier</a></span>\n";
echo "<input type='hidden' name='recurrence-hidden' id='recurrence-hidden' />\n";
echo "</td></tr>\n";

echo "<tr><td><label class='intitule'>Motif</label></td>\n";
echo "<td style='white-space:nowrap;'>";

echo "<select name='motif' id='motif' style='width:100%;' class='ui-widget-content ui-corner-all'>\n";
echo "<option value=''></option>\n";
foreach ($motifs as $elem) {
    $selected=html_entity_decode($elem['valeur'], ENT_QUOTES|ENT_IGNORE, "utf-8")==html_entity_decode($motif, ENT_QUOTES|ENT_IGNORE, "utf-8")?"selected='selected'":null;
    $class=$elem['type']==2?"padding20":"bold";
    $disabled=$elem['type']==1?"disabled='disabled'":null;
    $padding = $class == 'padding20' ? "&nbsp;&nbsp;&nbsp;" : null ;
    echo "<option value='".$elem['valeur']."' $selected class='$class' $disabled >$padding".$elem['valeur']."</option>\n";
}
echo "</select>\n";
if ($admin) {
    echo "<span class='pl-icon pl-icon-add' title='Ajouter' style='cursor:pointer;' id='add-motif-button'/>\n";
}
echo "</td></tr>\n";

echo "<tr $display_autre id='tr_motif_autre'><td><label class='intitule'>Motif (autre)</label></td>\n";
echo "<td><input type='text' name='motif_autre' style='width:100%;' value='$motif_autre' class='ui-widget-content ui-corner-all'/></td></tr>\n";

echo "<tr style='vertical-align:top;'><td>\n";
echo "<label class='intitule'>Commentaires</label></td><td>";
echo "<textarea name='commentaires' cols='25' rows='5' class='ui-widget-content ui-corner-all'>$commentaires</textarea>";
echo "</td></tr>";

if (in_array(701, $droits)) {
    echo "<tr style='vertical-align:top;'><td>\n";
    echo "<label class='intitule'>Pi&egrave;ces justificatives</label></td><td>";
    echo "<div class='absences-pj-fiche'>PJ1 <input type='checkbox' name='pj1' id='pj1' $pj1Checked /></div>";
    echo "<div class='absences-pj-fiche'>PJ2 <input type='checkbox' name='pj2' id='pj2' $pj2Checked /></div>";
    echo "<div class='absences-pj-fiche'>SO <input type='checkbox' name='so' id='so' $soChecked /></div>";
    echo "</td>\n";
}
echo "</tr>\n";

if ($config['Absences-validation']) {
    echo "<tr><td><label class='intitule'>Validation</label></td><td>\n";
    if ($admin) {
        echo "<select name='valide' class='ui-widget-content ui-corner-all'>\n";
        echo "<option value='0' $select1>Demand&eacute;e</option>\n";
        echo "<option value='2' $select4>Accept&eacute;e (En attente de validation hi&eacute;rarchique)</option>\n";
        echo "<option value='-2' $select5>Refus&eacute;e (En attente de validation hi&eacute;rarchique)</option>\n";
        if ($adminN2) {
            echo "<option value='1' $select2>Accept&eacute;e</option>\n";
            echo "<option value='-1' $select3>Refus&eacute;e</option>\n";
        }
        echo "</select>\n";
    } else {
        echo $validation_texte;
        echo "<input type='hidden' name='valide' value='$valide' />\n";
    }
    echo "</td></tr>\n";
}

echo <<<EOD
  <tr><td><label>Demande</label></td>
  <td>$demande</td></tr>
EOD;
echo "<tr><td colspan='2'><br/>\n";

// Si l'absence est importée depuis un agenda extérieur, on interdit la modification
if (($admin or ($valide==0 and $valideN1==0) or $config['Absences-validation']==0) and !$agenda_externe) {
    echo "<input type='button' class='ui-button' value='Supprimer' id='absence-bouton-supprimer' data-id='$id'/>";
    echo "&nbsp;&nbsp;\n";
    echo "<input type='button' class='ui-button' value='Annuler' onclick='annuler(1);'/>\n";
    echo "&nbsp;&nbsp;\n";
    echo "<input type='submit' class='ui-button' value='Valider'/>\n";
} else {
    echo "<a href='index.php?page=absences/voir.php' class='ui-button'>Retour</a>\n";
}
echo "</td></tr>\n";
echo "</table>\n";
echo "<input type='hidden' name='id' value='$id'/>";
echo "</form>\n";
?>

<!-- Dialog Box pour la modification de la récurrence -->
<div id="recurrence-form-tmp" title="Récurrence" class='noprint' style='display:none;'>
  <p>La modification des règles de récurrence n'est pas possible pour le moment.<br/>Cette fonctionnalité sera disponible dans les prochaines versions de Planning Biblio.</p>
</div>
<!-- Popup modification d'une récurrence -->
<div id="recurrence-alert" title="Modification d'une absence récurrente" class='noprint' style='display:none;'>
  <p>Souhaitez-vous modifier uniquement cet événement, tous les événements de la série, ou cet événement et ceux qui le suivent dans la série ?</p>
</div>
<!-- Popup suppression d'une récurrence -->
<div id="recurrence-alert-suppression" title="Suppression d'une absence récurrente" class='noprint' style='display:none;'>
  <p>Souhaitez-vous supprimer uniquement cet événement, tous les événements de la série, ou cet événement et ceux qui le suivent dans la série ?</p>
</div>

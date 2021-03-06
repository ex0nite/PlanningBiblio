<?php
/**
Planning Biblio, Version 2.8.1
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
@copyright 2011-2018 Jérôme Combes

Fichier : personnel/modif.php
Création : mai 2011
Dernière modification : 24 mai 2018
@author Jérôme Combes <jerome@planningbiblio.fr>

Description :
Affiche le formulaire permettant d'ajouter ou de modifier les agents.
Page séparée en 4 <div> (Général, Activités, Heures de présence, Droits d'accès. Ces <div> s'affichent lors des click sur
les onglets.
Ce formulaire est soumis au fichier personnel/valid.php

Cette page est appelée par le fichier index.php
*/

require_once "class.personnel.php";
require_once "activites/class.activites.php";
require_once "planningHebdo/class.planningHebdo.php";

// Initialisation des variables
$id=filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

$actif=null;
$admin=in_array(21, $droits)?true:false;
// NB : le champ poste et les fonctions postes_... sont utilisés pour l'attribution des activités (qualification)

// Gestion des droits d'accés
$db_groupes=new db();
$db_groupes->select2("acces", array("groupe_id", "groupe", "categorie", "ordre"), "groupe_id not in (99,100)", "group by groupe");

// Tous les droits d'accés
$groupes=array();
if ($db_groupes->result) {
    foreach ($db_groupes->result as $elem) {
        if (empty($elem['categorie'])) {
            $elem['categorie'] = 'Divers';
            $elem['ordre'] = '200';
        }
        $groupes[$elem['groupe_id']]=$elem;
    }
}

uasort($groupes, 'cmp_ordre');

// PlanningHebdo et EDTSamedi étant incompatibles, EDTSamedi est désactivé si PlanningHebdo est activé
if ($config['PlanningHebdo']) {
    $config['EDTSamedi']=0;
}

// Si multisites, les droits de gestion des absences, congés et modification planning dépendent des sites :
// on les places dans un autre tableau pour simplifier l'affichage
$groupes_sites=array();

if ($config['Multisites-nombre']>1) {
    for ($i = 2; $i <= 10; $i++) {

    // Exception, groupe 701 = pas de gestion multisites (pour le moment)
        if ($i == 7) {
            continue;
        }

        $groupe = ($i * 100) + 1 ;
        if (array_key_exists($groupe, $groupes)) {
            $groupes_sites[]=$groupes[$groupe];
            unset($groupes[$groupe]);
        }
    }
}

uasort($groupes_sites, 'cmp_ordre');


$db=new db();
$db->select2("select_statuts", null, null, "order by rang");
$statuts=$db->result;
$db=new db();
$db->select2("select_categories", null, null, "order by rang");
$categories=$db->result;
$db=new db();
$db->select2("personnel", "statut", null, "group by statut");
$statuts_utilises=array();
if ($db->result) {
    foreach ($db->result as $elem) {
        $statuts_utilises[]=$elem['statut'];
    }
}

// Liste des services
$services = array();
$db=new db();
$db->select2("select_services", null, null, "ORDER BY `rang`");
if ($db->result) {
    foreach ($db->result as $elem) {
        $services[]=$elem;
    }
}

// Liste des services utilisés
$services_utilises = array();
$db=new db();
$db->select2('personnel', 'service', null, "GROUP BY `service`");
if ($db->result) {
    foreach ($db->result as $elem) {
        $services_utilises[]=$elem['service'];
    }
}

$acces=array();
$postes_attribues=array();
$recupAgents=array("Prime","Temps");

if ($id) {		//	récupération des infos de l'agent en cas de modif
    $db=new db();
    $db->select2("personnel", "*", array("id"=>$id));
    $actif=$db->result[0]['actif'];
    $nom=$db->result[0]['nom'];
    $prenom=$db->result[0]['prenom'];
    $mail=$db->result[0]['mail'];
    $statut=$db->result[0]['statut'];
    $categorie=$db->result[0]['categorie'];
    $check_hamac = $db->result[0]['check_hamac'];
    $check_ics = json_decode($db->result[0]['check_ics'], true);
    $service=$db->result[0]['service'];
    $heuresHebdo=$db->result[0]['heures_hebdo'];
    $heuresTravail=$db->result[0]['heures_travail'];
    $arrivee=dateFr($db->result[0]['arrivee']);
    $depart=dateFr($db->result[0]['depart']);
    $login=$db->result[0]['login'];
    if ($config['PlanningHebdo']) {
        $p = new planningHebdo();
        $p->perso_id = $id;
        $p->debut = date("Y-m-d");
        $p->fin = date("Y-m-d");
        $p->valide = true;
        $p->fetch();
        if (!empty($p->elements)) {
            $temps = $p->elements[0]['temps'];
        } else {
            $temps = array();
        }
    } else {
        $temps=json_decode(html_entity_decode($db->result[0]['temps'], ENT_QUOTES|ENT_IGNORE, 'UTF-8'), true);
        if (!is_array($temps)) {
            $temps = array();
        }
    }
    $postes_attribues = json_decode(html_entity_decode($db->result[0]['postes'], ENT_QUOTES|ENT_IGNORE, 'UTF-8'), true);
    if (is_array($postes_attribues)) {
        sort($postes_attribues);
    }
    $acces=json_decode(html_entity_decode($db->result[0]['droits'], ENT_QUOTES|ENT_IGNORE, 'UTF-8'), true);
    $matricule=$db->result[0]['matricule'];
    $url_ics = $db->result[0]['url_ics'];
    $mailsResponsables=explode(";", html_entity_decode($db->result[0]['mails_responsables'], ENT_QUOTES|ENT_IGNORE, "UTF-8"));
    // $mailsResponsables : html_entity_decode necéssaire sinon ajoute des espaces après les accents ($mailsResponsables=join("; ",$mailsResponsables);)
    $informations=stripslashes($db->result[0]['informations']);
    $recup=stripslashes($db->result[0]['recup']);
    $sites=html_entity_decode($db->result[0]['sites'], ENT_QUOTES|ENT_IGNORE, 'UTF-8');
    $sites=$sites?json_decode($sites, true):array();
    $action="modif";
    $titre=$nom." ".$prenom;
  
    // URL ICS
    if ($config['ICS-Export']) {
        $p = new personnel();
        $p->CSRFToken = $CSRFSession;
        $ics = $p->getICSURL($id);
    }
} else {		// pas d'id, donc ajout d'un agent
    $id=null;
    $nom=null;
    $prenom=null;
    $mail=null;
    $statut=null;
    $categorie=null;
    $check_hamac = 1;
    $check_ics = array(1,1,1);
    $service=null;
    $heuresHebdo=null;
    $heuresTravail=null;
    $arrivee=null;
    $depart=null;
    $login=null;
    $temps=null;
    $postes_attribues=array();
    $access=array();
    $matricule=null;
    $url_ics=null;
    $mailsResponsables=array();
    $informations=null;
    $recup=null;
    $sites=array();
    $titre="Ajout d'un agent";
    $action="ajout";
    if ($_SESSION['perso_actif'] and $_SESSION['perso_actif']!="Supprim&eacute;") {
        $actif=$_SESSION['perso_actif'];
    }			// vérifie dans quel tableau on se trouve pour la valeur par défaut
}

$jours=array("Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche");
global $temps;
$contrats=array("Titulaire","Contractuel");

//		--------------		Début listes des activités		---------------------//
// Toutes les activités
$a=new activites();
$a->fetch();
$activites=$a->elements;

foreach ($activites as $elem) {
    $postes_completNoms[]=array($elem['nom'],$elem['id']);
    $postes_complet[]=$elem['id'];
}

$postes_dispo=array();		// les activités non attribuées (disponibles)
if ($postes_attribues) {
    $postes=join(",", $postes_attribues);	//	activités attribuées séparées par des virgules (valeur transmise à valid.php)
    if (is_array($postes_complet)) {
        foreach ($postes_complet as $elem) {
            if (!in_array($elem, $postes_attribues)) {
                $postes_dispo[]=$elem;
            }
        }
    }
} else {
    $postes="";	//	activités attribuées séparées par des virgules (valeur transmise à valid.php)
    $postes_dispo=$postes_complet;
}

// traduction en JavaScript du tableau postes_completNoms pour les fonctions seltect_add* et select_drop
$postes_completNoms_json = json_encode($postes_completNoms);
echo "<script type='text/JavaScript'>\n<!--\n";
echo "complet = JSON.parse('$postes_completNoms_json');\n";
echo "\n-->\n</script>\n";

    //	Ajout des noms dans les tableaux postes attribués et dispo
function postesNoms($postes, $tab_noms)
{
    $tmp=array();
    if (is_array($postes)) {
        foreach ($postes as $elem) {
            if (is_array($tab_noms)) {
                foreach ($tab_noms as $noms) {
                    if ($elem==$noms[1]) {
                        $tmp[]=array($elem,$noms[0]);
                        break;
                    }
                }
            }
        }
    }
    usort($tmp, "cmp_1");
    return $tmp;
}
$postes_attribues=postesNoms($postes_attribues, $postes_completNoms);
$postes_dispo=postesNoms($postes_dispo, $postes_completNoms);
//		--------------		Fin listes des postes		---------------------//

//		--------------		Début d'affichage			---------------------//
?>
<h3><?php echo $titre; ?></h3>
<!--		Menu						-->
<div class='ui-tabs'>
<ul>		
<li><a href='#main'>Infos générales</a></li>
<li><a href='#qualif'>Activités</a></li>
<li><a href='#temps' id='personnel-a-li3'>Heures de pr&eacute;sence</a></li>
<?php
if ($config['ICS-Server1'] or $config['ICS-Server2'] or $config['ICS-Server3'] or $config['ICS-Export']) {
    echo "<li><a href='#agendas'>Agendas et Synchronisation</a></li>";
}
if ($config['Conges-Enable']) {
    echo "<li><a href='#conges'>Cong&eacute;s</a></li>";
}
?>
<li><a href='#access'>Droits d'accès</a></li>
<?php
if (in_array(21, $droits)) {
    echo "<li class='ui-tab-cancel'><a href='index.php?page=personnel/index.php'>Annuler</a></li>\n";
    echo "<li class='ui-tab-submit'><a href='javascript:verif_form_agent();'>Valider</a></li>\n";
} else {
    echo "<li class='ui-tab-cancel'><a href='index.php?page=personnel/index.php'>Fermer</a></li>\n";
}
?>
</ul>

<?php
echo "<form method='post' action='index.php' name='form'>\n";
echo "<input type='hidden' name='page' value='personnel/valid.php' />\n";
echo "<input type='hidden' name='CSRFToken' value='$CSRFSession' />\n";
//			Début Infos générales
echo "<div id='main' style='margin-left:70px;padding-top:30px;'>\n";
echo "<input type='hidden' value='$action' name='action' />";
echo "<input type='hidden' value='$id' name='id' />";

echo "<table style='width:90%;'>";
echo "<tr valign='top'><td style='width:400px'>";
echo "Nom :";
echo "</td><td>";
echo in_array(21, $droits) ? "<input type='text' value='$nom' name='nom' id='nom' style='width:400px' />" : "<span id='nom'>$nom</span>" ;
echo "</td></tr>";

echo "<tr><td>";
echo "Prénom :";
echo "</td><td>";
echo in_array(21, $droits) ? "<input type='text' value='$prenom' name='prenom' id='prenom' style='width:400px' />" : "<span id='prenom'>$prenom</span>";
echo "</td></tr>";

echo "<tr><td>";
echo "E-mail : ";
if (in_array(21, $droits)) {
    echo "<a href='mailto:$mail'>$mail</a>";
}
echo "</td><td>";
echo in_array(21, $droits) ? "<input type='text' value='$mail' name='mail' id='mail' style='width:400px' />" : "<a href='mailto:$mail'><span id='mail'>$mail</span></a>";
echo "</td></tr>";

echo "<tr><td>";
echo "Statut :";
echo "</td><td style='white-space:nowrap'>";
if (in_array(21, $droits)) {
    echo "<select name='statut' id='statut' style='width:405px'>\n";
    echo "<option value=''>Aucun</option>\n";
    foreach ($statuts as $elem) {
        $select1=$elem['valeur']==$statut?"selected='selected'":null;
        echo "<option $select1 value='".$elem['valeur']."'>".$elem['valeur']."</option>\n";
    }
    echo "</select>\n";
    echo "<span class='pl-icon pl-icon-add' title='Ajouter' style='cursor:pointer; margin-left:4px;' id='add-statut-button'></span>\n";
} else {
    echo $statut;
}
echo "</td></tr>";

echo "<tr><td>";
echo "Contrat :";
echo "</td><td>";
if (in_array(21, $droits)) {
    echo "<select name='categorie' id='categorie' style='width:405px'>\n";
    echo "<option value=''>Aucun</option>\n";
    foreach ($contrats as $elem) {
        $select1=$elem==$categorie?"selected='selected'":null;
        echo "<option $select1 value='{$elem}'>{$elem}</option>\n";
    }
    echo "</select>\n";
} else {
    echo $categorie;
}
echo "</td></tr>";

echo "<tr><td>";
echo "Service de rattachement:";
echo "</td><td style='white-space:nowrap'>";
if (in_array(21, $droits)) {
    echo "<select name='service' id='service' style='width:405px'>\n";
    echo "<option value=''>Aucun</option>\n";
    foreach ($services as $elem) {
        $select1=$elem['valeur']==$service?"selected='selected'":null;
        echo "<option $select1 value='{$elem['valeur']}'>{$elem['valeur']}</option>\n";
    }
    echo "</select>\n";
    echo "<span class='pl-icon pl-icon-add' title='Ajouter' style='cursor:pointer; margin-left:4px;' id='add-service-button'></span>\n";
} else {
    echo $service;
}
echo "</td></tr>";
    

echo "<tr><td>";
echo "Heures de service public par semaine:";
echo "</td><td>";
if (in_array(21, $droits)) {
    echo "<select name='heuresHebdo' style='width:405px'  title='Choisissez un nombre d&apos;heures ou un pourcentage. Si vous Choisissez un pourcentage, le nombre d&apos;heures sera calculé à partir des plannings de pr&eacute;sence'>\n";
    echo "<option value='0'>&nbsp;</option>\n";

    for ($i=1;$i<101;$i++) {
        $select=$heuresHebdo=="$i%"?"selected='selected'":null;
        echo "<option $select value='$i%'>$i%</option>\n";
    }

    for ($i=1;$i<40;$i++) {
        $j=array();
    
        /**
         * @author Etienne Cavalié
         * Granularité = 5 minutes
         */
        if ($config['Granularite']==5) {
            $j[]=array($i,$i."h00");
            $j[]=array($i.".08",$i."h05");
            $j[]=array($i.".17",$i."h10");
            $j[]=array($i.".25",$i."h15");
            $j[]=array($i.".33",$i."h20");
            $j[]=array($i.".42",$i."h25");
            $j[]=array($i.".5",$i."h30");
            $j[]=array($i.".58",$i."h35");
            $j[]=array($i.".67",$i."h40");
            $j[]=array($i.".75",$i."h45");
            $j[]=array($i.".83",$i."h50");
            $j[]=array($i.".92",$i."h55");
        } elseif ($config['Granularite']==15) {
            $j[]=array($i,$i."h00");
            $j[]=array($i.".25",$i."h15");
            $j[]=array($i.".5",$i."h30");
            $j[]=array($i.".75",$i."h45");
        } elseif ($config['Granularite']==30) {
            $j[]=array($i,$i."h00");
            $j[]=array($i.".5",$i."h30");
        } else {
            $j[]=array($i,$i."h00");
        }
        foreach ($j as $elem) {
            $select=null;
            if (!strpos($heuresHebdo, "%") and $elem[0]==$heuresHebdo) {
                $select="selected='selected'";
            }
            echo "<option $select value='{$elem[0]}'>{$elem[1]}</option>\n";
        }
    }
    echo "</select>\n";
} else {
    echo $heuresHebdo;
    if (!stripos($heuresHebdo, "%")) {
        echo " heures";
    }
}
echo "</td></tr>";


echo "<tr><td>";
echo "Heures de travail par semaine:";
echo "</td><td>";
if (in_array(21, $droits)) {
    echo "<select name='heuresTravail' style='width:405px'>\n";
    echo "<option value='0'>&nbsp;</option>\n";
    for ($i=1;$i<40;$i++) {
        $j=array();
        if ($config['Granularite']==5) {
            $j[]=array($i,$i."h00");
            $j[]=array($i.".08",$i."h05");
            $j[]=array($i.".17",$i."h10");
            $j[]=array($i.".25",$i."h15");
            $j[]=array($i.".33",$i."h20");
            $j[]=array($i.".42",$i."h25");
            $j[]=array($i.".5",$i."h30");
            $j[]=array($i.".58",$i."h35");
            $j[]=array($i.".67",$i."h40");
            $j[]=array($i.".75",$i."h45");
            $j[]=array($i.".83",$i."h50");
            $j[]=array($i.".92",$i."h55");
        } elseif ($config['Granularite']==15) {
            $j[]=array($i,$i."h00");
            $j[]=array($i.".25",$i."h15");
            $j[]=array($i.".5",$i."h30");
            $j[]=array($i.".75",$i."h45");
        } elseif ($config['Granularite']==30) {
            $j[]=array($i,$i."h00");
            $j[]=array($i.".5",$i."h30");
        } else {
            $j[]=array($i,$i."h00");
        }
        foreach ($j as $elem) {
            $select=$elem[0]==$heuresTravail?"selected='selected'":"";
            echo "<option $select value='{$elem[0]}'>{$elem[1]}</option>\n";
        }
    }
    echo "</select>\n";
} else {
    echo $heuresTravail." heures";
}
echo "</td></tr>";

$select1=null;
$select2=null;
$select3=null;
$display=null;

switch ($actif) {
  case "Actif":		$select1="selected='selected'"; $actif2="Service public";	$display="style='display:none;'";	break;
  case "Inactif":		$select2="selected='selected'"; $actif2="Administratif";	$display="style='display:none;'";	break;
  case "Supprim&eacute;":	$select3="selected='selected'";	$actif2="Supprim&eacute;";	break;
  default:			$select1="selected='selected'"; $actif2="Service public";	$display="style='display:none;'";	break;
}
echo "<tr><td>";
echo "Service public / Administratif :";
echo "</td><td>";
if (in_array(21, $droits)) {
    echo "<select name='actif' style='width:405px'>\n";
    echo "<option $select1 value='Actif'>Service public</option>\n";
    echo "<option $select2 value='Inactif'>Administratif</option>\n";
    echo "<option $select3 value='Supprim&eacute;' $display>Supprim&eacute;</option>\n";
    echo "</select>\n";
} else {
    echo $actif2;
}
echo "</td></tr>";

// Multi-sites
if ($config['Multisites-nombre']>1) {
    echo "<tr style='vertical-align:top;'><td>Sites :</td><td>";
    for ($i=1;$i<=$config['Multisites-nombre'];$i++) {
        if (in_array(21, $droits)) {
            $checked=in_array($i, $sites)?"checked='checked'":null;
            echo "<input type='checkbox' name='sites[]' value='$i' $checked >{$config["Multisites-site$i"]}<br/>";
        } else {
            if (in_array($i, $sites)) {
                echo $config["Multisites-site{$i}"]."<br/>";
                ;
            }
        }
    }
    echo "</td></tr>\n";
}

echo "<tr><td>";
echo "Date d'arrivée ";
if (in_array(21, $droits)) {
    echo "</td><td>";
    echo "<input type='text' value='$arrivee' name='arrivee' style='width:400px' class='datepicker'/>";
} else {
    echo "</td><td>".$arrivee;
}
echo "</td></tr>";

echo "<tr><td>";
echo "Date de départ ";
if (in_array(21, $droits)) {
    echo "</td><td>";
    echo "<input type='text' value='$depart' name='depart' style='width:400px'  class='datepicker'/>";
} else {
    echo "</td><td>".$depart;
}
echo "</td></tr>";

echo "<tr><td>";
echo "Matricule : ";
echo "</td><td>";
echo in_array(21, $droits)?"<input type='text' value='$matricule' name='matricule' style='width:400px' />":"$matricule</a>";
echo "</td></tr>";

echo "<tr><td>";
echo "E-mails des responsables : ";
if (in_array(21, $droits)) {
    foreach ($mailsResponsables as $elem) {
        $elem=trim($elem);
        echo "<br/><a href='mailto:$elem' style='margin-left:30px;'>$elem</a>";
    }
}
echo "</td><td>";
if (in_array(21, $droits)) {
    $mailsResponsables=join("; ", $mailsResponsables);
    echo "<textarea name='mailsResponsables' style='width:400px' cols='10' rows='4'>$mailsResponsables</textarea>";
} else {
    foreach ($mailsResponsables as $elem) {
        $elem=trim($elem);
        echo "<a href='mailto:$elem' style='margin-left:30px;'>$elem</a><br/>";
    }
}
echo "</td></tr>";

echo "<tr style='vertical-align:top;'><td>";
echo "Informations :";
echo "</td><td>";
echo in_array(21, $droits)?"<textarea name='informations' style='width:400px' cols='10' rows='4'>$informations</textarea>":str_replace("\n", "<br/>", $informations);
echo "</td></tr>";

if ($config['Recup-Agent']) {
    echo "<tr style='vertical-align:top;'><td>";
    echo "Récupération du samedi :";
    echo "</td><td>";
    if ($config['Recup-Agent']=="Texte" and in_array(21, $droits)) {
        echo "<textarea name='recup' style='width:400px' cols='10' rows='4'>$recup</textarea>";
    }
    if (htmlentities($config['Recup-Agent'], ENT_QUOTES|ENT_IGNORE, "UTF-8", false)=="Menu d&eacute;roulant" and in_array(21, $droits)) {
        echo "<select name='recup' style='width:400px'>\n";
        echo "<option value=''>&nbsp;</option>\n";
        foreach ($recupAgents as $elem) {
            $selected=$recup==$elem?"selected='selected'":null;
            echo "<option value='$elem' $selected>$elem</option>\n";
        }
        echo "</select>\n";
    }
    if (!in_array(21, $droits)) {
        echo str_replace("\n", "<br/>", $recup);
    }
    echo "</td></tr>";
}

if ($id) {
    echo "<tr><td>\n";
    echo "Login :";
    echo "</td><td>";
    echo $login;
    echo "</td></tr>";
    if (in_array(21, $droits)) {
        echo "<tr><td colspan='2'>\n";
        echo "<a href='javascript:modif_mdp();'>Changer le mot de passe</a>";
        echo "</td></tr>";
    }
}
?>
</table>
</div>
<!--	Fin Info générales	-->

<!--	Début Qualif	-->
<div id='qualif' style='margin-left:70px;display:none;padding-top:30px;'>
<table style='width:90%;'>
<tr style='vertical-align:top;'><td>
<b>Activités disponibles</b><br/>
<div id='dispo_div'>
<?php
if (in_array(21, $droits)) {
    echo "<select id='postes_dispo' name='postes_dispo' style='width:300px;' size='20' multiple='multiple'>\n";
    foreach ($postes_dispo as $elem) {
        echo "<option value='{$elem[0]}'>{$elem[1]}</option>\n";
    }
    echo "</select>\n";
} else {
    echo "<ul>\n";
    foreach ($postes_dispo as $elem) {
        echo "<li>{$elem[1]}</li>\n";
    }
    echo "</ul>\n";
}
?>
</div>
<?php
if (in_array(21, $droits)) {
    echo "</td><td style='text-align:center;padding-top:100px;'>\n";
    echo "<input type='button' style='width:200px' value='Attribuer >>' onclick='select_add(\"postes_dispo\",\"postes_attribues\",\"postes\",300);' /><br/><br/>\n";
    echo "<input type='button' style='width:200px' value='Attribuer Tout >>' onclick='select_add_all(\"postes_dispo\",\"postes_attribues\",\"postes\",300);' /><br/><br/>\n";
    echo "<input type='button' style='width:200px' value='<< Supprimer' onclick='select_drop(\"postes_dispo\",\"postes_attribues\",\"postes\",300);' /><br/><br/>\n";
    echo "<input type='button' style='width:200px' value='<< Supprimer Tout' onclick='select_drop_all(\"postes_dispo\",\"postes_attribues\",\"postes\",300);' /><br/><br/>\n";
}
?>
</td><td>
<b>Activités attribu&eacute;es</b><br/>
<div id='attrib_div'>
<?php
if (in_array(21, $droits)) {
    echo "<select id='postes_attribues' name='postes_attribues' style='width:300px;' size='20' multiple='multiple'>\n";
    foreach ($postes_attribues as $elem) {
        echo "<option value='{$elem[0]}'>{$elem[1]}</option>\n";
    }
    echo "</select>\n";
} else {
    echo "<ul>\n";
    foreach ($postes_attribues as $elem) {
        echo "<li>{$elem[1]}</li>\n";
    }
    echo "</ul>\n";
}
?>
</div>
<input type='hidden' name='postes' id='postes' value='<?php echo $postes;?>'/>
</td></tr>
</table>
</div>
<!--	FIN Qualif	-->

<!--	Heures de présence		-->
<div id='temps' style='margin-left:70px;display:none;padding-top:30px;'>
<?php
switch ($config['nb_semaine']) {
  case 2: $cellule=array("Semaine Impaire","Semaine Paire");		break;
  case 3: $cellule=array("Semaine 1","Semaine 2","Semaine 3");		break;
  default: $cellule=array("Jour");					break;
}
$fin=$config['Dimanche']?array(8,15,22):array(7,14,21);
$debut=array(1,8,15);

if ($config['EDTSamedi'] == 1) {
    $config['nb_semaine'] = 2;
    $cellule = array("Semaine standard", "Semaine<br/>avec samedi");
    $table_name = array('Emploi du temps standard', 'Emploi du temps des semaines avec samedi travaillé');
} elseif ($config['EDTSamedi'] == 2) {
    $config['nb_semaine'] = 3;
    $cellule=array("Semaine standard", "Semaine<br/>avec samedi", "Semaine<br/>ouverture restreinte");
    $table_name = array('Emploi du temps standard', 'Emploi du temps des semaines avec samedi travaillé', 'Emploi du temps en ouverture restreinte');
}

for ($j=0;$j<$config['nb_semaine'];$j++) {
    if ($config['EDTSamedi']) {
        echo "<br/><b>{$table_name[$j]}</b>";
    }
    echo "<table border='1' cellspacing='0'>\n";
    echo "<tr style='text-align:center;'><td style='width:135px;'>{$cellule[$j]}</td><td style='width:135px;'>Heure d'arrivée</td>";
    if ($config['PlanningHebdo-Pause2']) {
        echo "<td style='width:135px;'>Début de pause 1</td><td style='width:135px;'>Fin de pause 1</td>";
        echo "<td style='width:135px;'>Début de pause 2</td><td style='width:135px;'>Fin de pause 2</td>";
    } else {
        echo "<td style='width:135px;'>Début de pause</td><td style='width:135px;'>Fin de pause</td>";
    }
    echo "<td style='width:135px;'>Heure de départ</td>";
    if ($config['Multisites-nombre']>1) {
        echo "<td>Site</td>";
    }
  
    echo "<td style='width:135px;'>Temps</td>";
    echo "</tr>\n";
    for ($i=$debut[$j];$i<$fin[$j];$i++) {
        $k=$i-($j*7)-1;
        if (in_array(21, $droits) and !$config['PlanningHebdo']) {
            echo "<tr><td>{$jours[$k]}</td>\n";
            echo "<td>".selectTemps($i-1, 0, null, "select$j")."</td>\n";
            echo "<td>".selectTemps($i-1, 1, null, "select$j")."</td>\n";
            echo "<td>".selectTemps($i-1, 2, null, "select$j")."</td>\n";
            if ($config['PlanningHebdo-Pause2']) {
                echo "<td>".selectTemps($i-1, 5, null, "select$j")."</td>\n";
                echo "<td>".selectTemps($i-1, 6, null, "select$j")."</td>\n";
            }
            echo "<td>".selectTemps($i-1, 3, null, "select$j")."</td>\n";
            if ($config['Multisites-nombre']>1) {
                echo "<td><select name='temps[".($i-1)."][4]' class='edt-site'>\n";
                echo "<option value='' class='edt-site-0'>&nbsp;</option>\n";
                for ($l=1;$l<=$config['Multisites-nombre'];$l++) {
                    $selected = (isset($temps[$i-1][4]) and $temps[$i-1][4]==$l) ? "selected='selected'" : null;
                    echo "<option value='$l' $selected class='edt-site-$l'>{$config["Multisites-site{$l}"]}</option>\n";
                }
                echo "</select></td>";
            }
            echo "<td id='heures_{$j}_$i'></td>\n";
            echo "</tr>\n";
        } else {
            echo "<tr><td>{$jours[$k]}</td>\n";
      
            for ($l=0; $l<3; $l++) {
                $heure = isset($temps[$i-1][0]) ? heure2($temps[$i-1][$l]) : null;
                echo "<td id='temps_".($i-1)."_$l'>$heure</td>\n";
            }

            if ($config['PlanningHebdo-Pause2']) {
                for ($l=5; $l<7; $l++) {
                    $heure = isset($temps[$i-1][$l]) ? heure2($temps[$i-1][$l]) : null;
                    echo "<td id='temps_".($i-1)."_$l'>$heure</td>\n";
                }
            }

            $heure = isset($temps[$i-1][0]) ? heure2($temps[$i-1][3]) : null;
            echo "<td id='temps_".($i-1)."_3'>$heure</td>\n";


            if ($config['Multisites-nombre']>1) {
                $site=null;
                if (isset($temps[$i-1][4])) {
                    $site="Multisites-site".$temps[$i-1][4];
                    $site = isset($config[$site]) ? $config[$site] : null;
                }
                echo "<td>$site</td>";
            }
            echo "<td id='heures_{$j}_$i'></td>\n";
            echo "</tr>\n";
        }
    }
    echo "</table>\n";
    echo "Total : <font style='font-weight:bold;' id='heures$j'></font><br/><br/>\n";
}

// EDTSamedi : emploi du temps différents les semaines avec samedi travaillé
// Choix des semaines avec samedi travaillé
if ($config['EDTSamedi']) {
    // Recherche des semaines avec samedi travaillé entre le 1er septembre de N-1 et le 31 août de N+3
    $d=new datePl((date("Y")-1)."-09-01");
    $premierLundi=$d->dates[0];
    $d=new datePl((date("Y")+3)."-08-31");
    $dernierLundi=$d->dates[0];

    $p=new personnel();
    $p->fetchEDTSamedi($id, $premierLundi, $dernierLundi);
    $edt=$p->elements;

    // inputs premierLundi et dernierLundi pour mise à jour (validation=suppression et insertion des nouveaux élements)
    echo "<input type='hidden' name='premierLundi' value='$premierLundi' />\n";
    echo "<input type='hidden' name='dernierLundi' value='$dernierLundi' />\n";
    echo "<div id='EDTChoix'>\n";
    echo "<h3>Choix des emplois du temps</h3>\n";

    if ($config['EDTSamedi'] == 1) {
        echo "<p>Cochez les semaines avec le samedi travaill&eacute;</p>\n";
    } elseif ($config['EDTSamedi'] == 2) {
        echo "<p>Pour chaque semaine, cochez s'il s'agit d'une semaine : standard (STD) / avec samedi travaill&eacute; (SAM) / ouverture restreinte (RES)</p>\n";
    }

    echo "<div id='EDTTabs'>\n";
    echo "<ul>";
    for ($i=0;$i<4;$i++) {
        $annee=(date("Y")+$i-1)."-".(date("Y")+$i);
        echo "<li><a href='#EDTTabs-$i' id='EDTA-$i'>Année $annee</a></li>\n";
    }
    echo "</ul>\n";

    for ($i=0;$i<4;$i++) {
        $d=new datePl((date("Y")-1+$i)."-09-01");
        $premierLundi=$d->dates[0];
        $d=new datePl((date("Y")+$i)."-08-31");
        $dernierLundi=$d->dates[0];

        if (date("Y-m-d")>=$premierLundi and date("Y-m-d")<=$dernierLundi) {
            $currentTab="#EDTA-$i";
        }
        $current=$premierLundi;
        $j=0;

        echo "<div id=EDTTabs-$i>";
        echo "<table class='tableauStandard'>";
        echo "<tr><td>";

        while ($current<=$dernierLundi) {
            // Evite de mettre la même semaine (fin août - début septembre) dans 2 années universitaires
            if (isset($last) and $current==$last) {
                $last=$current;
                $current=date("Y-m-d", strtotime("+7 day", strtotime($current)));
                continue;
            }
            $lundi=date("d/m/Y", strtotime($current));
            $dimanche=date("d/m/Y", strtotime("+6 day", strtotime($current)));
            $semaine=date("W", strtotime($current));
            echo "S$semaine : $lundi &rarr; $dimanche";

            // Si config['EDTSamedi'] == 1 (Emploi du temps différent les semaines avec samedi travaillé)
            if ($config['EDTSamedi'] == 1) {
                $checked = array_key_exists($current, $edt) ? "checked='checked'" : null ;
                echo "<input type='checkbox' value='$current' name='EDTSamedi[]' $checked /><br/>\n";
            }

            // Si config['EDTSamedi'] == 2 (Emploi du temps différent les semaines avec samedi travaillé et en ouverture restreinte)
            elseif ($config['EDTSamedi'] == 2) {
                $checked1 = "checked='checked'";
                $checked2 = null;
                $checked3 = null;

                if (array_key_exists($current, $edt)) {
                    $checked1 = null;
          
                    if ($edt[$current]['tableau'] == 2) {
                        $checked2 = "checked='checked'";
                    } elseif ($edt[$current]['tableau'] == 3) {
                        $checked3 = "checked='checked'";
                    }
                }

                echo "<span style='margin-left:20px;'>\n";
                echo "<input type='radio' value='1' name='EDTSamedi_$current' $checked1 id='radio_{$current}_STD'/> <label for='radio_{$current}_STD' >STD</label>\n";
                echo "<input type='radio' value='2' name='EDTSamedi_$current' $checked2 id='radio_{$current}_SAM'/> <label for='radio_{$current}_SAM' >SAM</label>\n";
                echo "<input type='radio' value='3' name='EDTSamedi_$current' $checked3 id='radio_{$current}_RES'/> <label for='radio_{$current}_RES' >RES</label>\n";
                echo "</span>\n";
                echo "<br/>\n";
            }

            if ($j==17 or $j==35) {
                echo "</td><td>";
            }
            $j++;
            $last=$current;
            $current=date("Y-m-d", strtotime("+7 day", strtotime($current)));
        }
        echo "</td></tr>\n";
        echo "</table>\n";
        echo "</div>\n";
    }
    echo "</div>\n";
    echo "</div>\n";
}
?>

</div>
<!--	FIN Heures de présence-->

<!--	Agendas		-->
<div id='agendas' style='margin-left:70px;display:none;padding-top:30px;'>
<?php
echo "<table style='width:90%;'>";

//
if ($config['Hamac-csv']) {
    $hamac_pattern = !empty($config['Hamac-motif']) ? $config['Hamac-motif'] : 'Hamac';
    $checked = !empty($check_hamac) ? "checked='checked'" : null;
    $checked2 = $checked ? "Oui" : "Non";
    $class = $checked ? "green bold" : "red";

    echo "<tr><td style='width:400px'>";
    echo "Synchronisation $hamac_pattern : ";
    echo "</td><td>";
    echo in_array(21, $droits)?"<input type='checkbox' value='1' name='check_hamac' $checked />":"<span class='agent-acces-checked2 $class'>$checked2</span>\n";

    echo "</td></tr>";
}

if ($config['ICS-Server1']) {
    $ics_pattern = !empty($config['ICS-Pattern1']) ? $config['ICS-Pattern1'] : 'Serveur ICS N&deg;1';
    $checked = !empty($check_ics[0]) ? "checked='checked'" : null;
    $checked2 = $checked ? "Oui" : "Non";
    $class = $checked ? "green bold" : "red";

    echo "<tr><td style='width:400px'>";
    echo "Synchronisation de l'agenda ICS $ics_pattern : ";
    echo "</td><td>";
    echo in_array(21, $droits)?"<input type='checkbox' value='1' name='check_ics1' $checked />":"<span class='agent-acces-checked2 $class'>$checked2</span>\n";
 
    echo "</td></tr>";
}

if ($config['ICS-Server2']) {
    $ics_pattern = !empty($config['ICS-Pattern2']) ? $config['ICS-Pattern2'] : 'Serveur ICS N&deg;2';
    $checked = !empty($check_ics[1]) ? "checked='checked'" : null;
    $checked2 = $checked ? "Oui" : "Non";
    $class = $checked ? "green bold" : "red";

    echo "<tr><td style='width:400px'>";
    echo "Agenda ICS $ics_pattern : ";
    echo "</td><td>";
    echo in_array(21, $droits)?"<input type='checkbox' value='1' name='check_ics2' $checked />":"<span class='agent-acces-checked2 $class'>$checked2</span>\n";
 
    echo "</td></tr>";
}

// URL du flux ICS à importer
if ($config['ICS-Server3']) {
    $checked = !empty($check_ics[2]) ? "checked='checked'" : null;
    $checked2 = $checked ? "Oui" : "Non";
    $class = $checked ? "green bold" : "red";

    echo "<tr><td style='width:400px'>";
    echo "Agenda ICS distant : ";
    echo "</td><td>";
    echo in_array(21, $droits)?"<input type='checkbox' value='1' name='check_ics3' $checked />":"<span class='agent-acces-checked2 $class'>$checked2</span>\n";
    echo in_array(21, $droits)?"<input type='text' value='$url_ics' name='url_ics' style='width:400px; margin-left:20px;' />":"<span style='margin-left:20px;'>$url_ics</span>\n";
    echo "</td></tr>";
}

// URL du fichier ICS Planning Biblio
if ($id and isset($ics)) {
    echo "<tr><td style='padding-top: 20px;'>Agenda ICS Planning Biblio</td>\n";
    echo "<td style='padding-top: 20px;' id='url-ics'>$ics</td></tr>\n";
    if ($config['ICS-Code']) {
        echo "<tr><td>&nbsp;</td>\n";
        echo "<td><a href='javascript:resetICSURL($id, \"$CSRFSession\", \"$prenom $nom\");'>R&eacute;initialiser l'URL</a></td></tr>\n";
    }
    echo "<tr><td>&nbsp;</td>\n";
    echo "<td><a href='javascript:sendICSURL();'>Envoyer l'URL &agrave; l&apos;agent par e-mail ($mail)</a></td></tr>\n";
}
echo "</table>\n";
?>
</div>
<!--	FIN Agendas		-->

<!--	Droits d'accès		-->
<div id='access' style='margin-left:70px;display:none;padding-top:30px;'>
<?php
if (!$admin) {
    echo "<ul>\n";
}

// Affichage de tous les droits d'accès si un seul site ou des droits d'accès ne dépendant pas des sites
$last_category = null;

foreach ($groupes as $elem) {
    // N'affiche pas les droits d'accès à la configuration (réservée au compte admin)
    if ($elem['groupe_id']==20) {
        continue;
    }

    // N'affiche pas les droits de gérer les congés si le module n'est pas activé
    if (!$config['Conges-Enable'] and in_array($elem['groupe_id'], array(401, 601))) {
        continue;
    }

    // N'affiche pas les droits de gérer les plannings de présence si le module n'est pas activé
    if (!$config['PlanningHebdo'] and $elem['groupe_id']==24) {
        continue;
    }

    // Affichage des catégories
    if ($elem['categorie'] != $last_category) {
        echo "<h3 style='margin:10px 0 5px 0;'>{$elem['categorie']}</h3>\n";
    }
    $last_category = $elem['categorie'];
  
    //	Affichage des lignes avec checkboxes
    if (is_array($acces)) {
        $checked=in_array($elem['groupe_id'], $acces)?"checked='checked'":null;
        $checked2=$checked?"Oui":"Non";
        $class=$checked?"green bold":"red";
    }
    if ($admin) {
        echo "<input type='checkbox' name='droits[]' $checked value='{$elem['groupe_id']}' style='margin:0 10px 0 20px;'/>{$elem['groupe']}<br/>\n";
    } else {
        echo "<li>{$elem['groupe']} <label class='agent-acces-checked2 $class'>$checked2</label></li>\n";
    }
}
if (!$admin) {
    echo "</ul>\n";
}

// Affichage des droits d'accès dépendant des sites (si plusieurs sites)
if ($config['Multisites-nombre']>1) {
    echo "<table style='margin-top:50px;'><thead><tr><th>&nbsp;</th>\n";
    for ($i=1;$i<=$config['Multisites-nombre'];$i++) {
        echo "<th class='center' style='padding:0 10px;'>{$config["Multisites-site$i"]}</th>\n";
    }
    echo "</tr></thead>\n";
    echo "<tbody>\n";

    $last_category = null;
    foreach ($groupes_sites as $elem) {

        // N'affiche pas les droits de gérer les congés si le module n'est pas activé
        if (!$config['Conges-Enable'] and in_array($elem['groupe_id'], array(401, 601))) {
            continue;
        }

        // Affichage des catégories
        if ($elem['categorie'] != $last_category) {
            echo "<tr><td><h3 style='margin:10px 0 5px 0;'>{$elem['categorie']}</h3></td>\n";
        }
        $last_category = $elem['categorie'];

        echo "<tr><td>{$elem['groupe']}</td>\n";

        for ($i=1;$i<$config['Multisites-nombre']+1;$i++) {
            $site=$config['Multisites-site'.$i];

            $groupe_id = $elem['groupe_id'] - 1 + $i;

            $checked=null;
            $checked="Non";
            if (is_array($acces)) {
                $checked=in_array($groupe_id, $acces)?"checked='checked'":null;
                $checked2=$checked?"Oui":"Non";
                $class=$checked?"green bold":"red";
            }

            if ($admin) {
                echo "<td class='center'><input type='checkbox' name='droits[]' $checked value='$groupe_id' /></td>\n";
            } else {
                echo "<td class='center $class'>$checked2</td>\n";
            }
        }
        echo "</tr>\n";
    }
    echo "<tbody></table>\n";
}


?>
</div>
<!--	FIN Droits d'accès		-->

<?php
if ($config['Conges-Enable']) {
    include "conges/ficheAgent.php";
}
?>
</div>	<!-- .ui-tabs	-->
</form>


<!--	Modification de la liste des statuts (Dialog Box) -->  
<div id="add-statut-form" title="Liste des statuts" class='noprint'>
  <p class="validateTips">Ajoutez, supprimez des statuts. Modifiez leur catégorie. Modifiez l'ordre des statuts dans les menus déroulant.</p>
  <form>
  <p><input type='text' id='add-statut-text' style='width:300px;'/>
    <input type='button' id='add-statut-button2' class='ui-button' value='Ajouter' style='margin-left:15px;'/></p>
  <fieldset>
    <ul id="statuts-sortable">
<?php
    if (is_array($statuts)) {
        foreach ($statuts as $elem) {
            echo "<li class='ui-state-default' id='li_{$elem['id']}'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span>\n";
            echo "<font id='valeur_{$elem['id']}'>{$elem['valeur']}</font>\n";
            echo "<select id='categorie_{$elem['id']}' style='position:absolute;left:330px;'>\n";
            echo "<option value='0'>&nbsp;</option>\n";
            foreach ($categories as $elem2) {
                $selected=$elem2['id']==$elem['categorie']?"selected='selected'":null;
                echo "<option value='{$elem2['id']}' $selected>{$elem2['valeur']}</option>\n";
            }
            echo "</select>\n";
            if (!in_array($elem['valeur'], $statuts_utilises)) {
                echo "<span class='ui-icon ui-icon-trash' style='position:relative;left:455px;top:-20px;cursor:pointer;' onclick='$(this).closest(\"li\").hide();'></span>\n";
            }
            echo "</li>\n";
        }
    }
?>
    </ul>
  </fieldset>
  </form>
</div>

<!--	Modification de la liste des services (Dialog Box) -->  
<div id="add-service-form" title="Liste des services" class='noprint' style='display:none;' >
  <p class="validateTips">Ajoutez, supprimez et modifiez l'ordre des services dans le menu déroulant.</p>
  <form>
  <p><input type='text' id='add-service-text' style='width:300px;'/>
    <input type='button' id='add-service-button2' class='ui-button' value='Ajouter' style='margin-left:15px;'/></p>
  <fieldset>
    <ul id="services-sortable">
<?php
    if (is_array($services)) {
        foreach ($services as $elem) {
            echo "<li class='ui-state-default' id='li_{$elem['id']}'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span>\n";
            echo "<font id='valeur_{$elem['id']}'>{$elem['valeur']}</font>\n";

            if (!in_array($elem['valeur'], $services_utilises)) {
                echo "<span class='ui-icon ui-icon-trash' style='position:relative;left:455px;top:-20px;cursor:pointer;' onclick='$(this).closest(\"li\").hide();'></span>\n";
            }
            echo "</li>\n";
        }
    }
?>
    </ul>
  </fieldset>
  </form>
</div>

<!-- Envoi de l'URL ICS par mail -->
<div id="ics-url-form" title="Envoi de l'URL de l'agenda Planning Biblio" class='noprint' style='display:none;'>
  <p class="validateTips">Envoyez à l'agent l'URL de son agenda Planning Biblio.</p>
  <form>
  <strong>Destinataire</strong><br/>
  <span id='ics-url-recipient'>&nbsp;</span><br/><br/>
  <label for='ics-url-subject'>Sujet</label><br/>
  <input type='text' id='ics-url-subject' name='ics-url-subject' value='<?php echo $lang['send_ics_url_subject']; ?>'/><br/><br/>
  <label for='ics-url-text'>Message</label><br/>
  <textarea id='ics-url-text' name='ics-url-text'><?php echo $lang['send_ics_url_message']; ?></textarea>
  </form>
</div>

<script type='text/JavaScript'>
<!--
// Affichage du choix des semaines avec samedi travaillé avec onglets
// Et sélection de l'onglet correspondant à l'année en cours
<?php
if ($config['EDTSamedi']) {
    echo "$(\"#EDTTabs\").tabs();\n";
    echo "$(\"$currentTab\").click();\n";
}

// Affichage du nombre d'heures correspondant à chaque emploi du temps
for ($i=0;$i<$config['nb_semaine'];$i++) {
    echo "$(\".select$i\").change(function(){calculHeures($(this),\"\",\"form\",\"heures$i\",$i);});\n";
    echo "$(\"document\").ready(function(){calculHeures($(this),\"\",\"form\",\"heures$i\",$i);});\n";
}
?>
-->
</script>
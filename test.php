<?php

require_once "absences/class.absences.php";
require_once "planningHebdo/class.planningHebdo.php";
require_once "planningHebdo/planning.php";

// Date
$date=filter_input(INPUT_GET,"date",FILTER_CALLBACK,array("options"=>"sanitize_dateSQL"));
if(!$date and array_key_exists('PLdate',$_SESSION)){
  $date=$_SESSION['PLdate'];
}
elseif(!$date and !array_key_exists('PLdate',$_SESSION)){
  $date=date("Y-m-d");
}
$_SESSION['PLdate']=$date;
$dateFr=dateFr($date);

$dateFr=dateFr($date);
$d=new datePl($date);
$semaine=$d->semaine;
$semaine3=$d->semaine3;
$jour=$d->jour;
$dates=$d->dates;
$datesSemaine=join(",",$dates);
$j1=$dates[0];
$j2=$dates[1];
$j3=$dates[2];
$j4=$dates[3];
$j5=$dates[4];
$j6=$dates[5];
$j7=$dates[6];
$dateAlpha=dateAlpha($date);


// Affichage des absences
$a=new absences();
$a->valide=true;
$a->fetch("`nom`,`prenom`,`debut`,`fin`",null,null,$date,$date);
$absences=$a->elements;

// Ajout des congés

if(in_array("conges",$plugins)){
  include "plugins/conges/planning_cellules.php";
  include "plugins/conges/planning.php";
}

// Tri des absences par nom
usort($absences,"cmp_nom_prenom_debut_fin");

// Sélection des agents présents
$heures=null;
$presents=array();
$absents=array(2);	// 2 = Utilisateur "Tout le monde", on le supprime

// On exclus ceux qui sont absents toute la journée
if(!empty($absences)){
  foreach($absences as $elem){
    if($elem['debut']<=$date." 00:00:00" and $elem['fin']>=$date." 23:59:59"){
      $absents[]=$elem['perso_id'];
    }
  }
}

// recherche des personnes à exclure (ne travaillant ce jour)
$db=new db();
$dateSQL=$db->escapeString($date);
$db->select("personnel","*","`actif` LIKE 'Actif' AND (`depart` > $dateSQL OR `depart` = '0000-00-00')","ORDER BY `nom`,`prenom`");

$verif=true;	// verification des heures des agents
if(!$config['ctrlHresAgents'] and ($d->position==6 or $d->position==0)){
  $verif=false; // on ne verifie pas les heures des agents le samedi et le dimanche (Si ctrlHresAgents est desactivé)
}

// Si il y a des agents et verification des heures de présences
if($db->result and $verif){

  // Si module PlanningHebdo : recherche des plannings correspondant à la date actuelle
  if($config['PlanningHebdo']){
    include "planningHebdo/planning.php";
  }

  // Pour chaque agent
  foreach($db->result as $elem){
    $heures=null;

    // Récupération du planning de présence
    $temps=array();

    // Si module PlanningHebdo : emploi du temps récupéré à partir de planningHebdo
    if($config['PlanningHebdo']){
      if(array_key_exists($elem['id'],$tempsPlanningHebdo)){
	$temps=$tempsPlanningHebdo[$elem['id']];
      }
    }else{
      // Emploi du temps récupéré à partir de la table personnel
      $temps=unserialize($elem['temps']);
    }

    $jour=$d->position-1;		// jour de la semaine lundi = 0 ,dimanche = 6
    if($jour==-1){
      $jour=6;
    }

    // Si semaine paire, position +7 : lundi A = 0 , lundi B = 7 , dimanche B = 13
    if($config['nb_semaine']=="2" and !($semaine%2)){
      $jour+=7;
    }
    // Si utilisation de 3 plannings hebdo
    elseif($config['nb_semaine']=="3"){
      if($semaine3==2){
	$jour+=7;
      }
      elseif($semaine3==3){
	$jour+=14;
      }
    }

    // Si l'emploi du temps est renseigné
    if(!empty($temps) and array_key_exists($jour,$temps)){
      // S'il y a une heure de début (matin ou midi)
      if($temps[$jour][0] or $temps[$jour][2]){
	$heures=$temps[$jour];
      }
    }

    // S'il y a des horaires correctement renseignés
    $siteAgent=null;
    if($heures and !in_array($elem['id'],$absents)){
      if($config['Multisites-nombre']>1){
	if(isset($heures[4])){
	  $siteAgent=$config['Multisites-site'.$heures[4]];
	}
      }
      $siteAgent=$siteAgent?$siteAgent.", ":null;


      $horaires=null;
      if(!$heures[1] and !$heures[2]){		// Pas de pause le midi
	$horaires=heure2($heures[0])." - ".heure2($heures[3]);
      }
      elseif(!$heures[2] and !$heures[3]){	// matin seulement
	$horaires=heure2($heures[0])." - ".heure2($heures[1]);
      }
      elseif(!$heures[0] and !$heures[1]){	// après midi seulement
	$horaires=heure2($heures[2])." - ".heure2($heures[3]);
      }
      else{		// matin et après midi avec pause
	$horaires=heure2($heures[0])." - ".heure2($heures[1])." &amp; ".heure2($heures[2])." - ".heure2($heures[3]);
      }
      $presents[]=array("id"=>$elem['id'],"nom"=>$elem['nom']." ".$elem['prenom'],"site"=>$siteAgent,"heures"=>$horaires, 
      "h0"=>$heures[0], "h1"=>$heures[1], "h2"=>$heures[2], "h3"=>$heures[3] );
    }
  }
}

usort($presents,"cmp_h0_h2");

echo "<table class='tableauStandard'>\n";
echo "<tr><td><h3 style='text-align:left;margin:40px 0 0 0;'>Liste des présents</h3></td>\n";
if(!empty($absences)){
  echo "<td><h3 style='text-align:left;margin:40px 0 0 0;'>Liste des absents</h3></td>";
}
echo "</tr>\n";

// Liste des présents
echo "<tr style='vertical-align:top;'><td>";
echo "<table cellspacing='0'> ";
$class="tr1";
foreach($presents as $elem){
  $class=$class=="tr1"?"tr2":"tr1";
  echo "<tr class='$class'><td>{$elem['nom']}</td><td style='padding-left:15px;'>{$elem['site']}</td>\n";
  echo "<td>{$elem['h0']}</td><td>{$elem['h1']}</td><td>{$elem['h2']}</td><td>{$elem['h3']}</td>\n";

  $last=false;

  for($h=8;$h<24;$h++){
    for($m=0;$m<60;$m=$m+5){

      $min=sprintf("%'.02d", $h).":".sprintf("%'.02d", $m);
      $max=$m<55?sprintf("%'.02d", $h).":".sprintf("%'.02d", $m+5):sprintf("%'.02d", $h+1).":00";
      $h0=substr($elem['h0'],0,5);
      $h1=substr($elem['h1'],0,5);
      $h2=substr($elem['h2'],0,5);
      $h3=substr($elem['h3'],0,5);

      if(!$h1 and !$h2){		// Pas de pause le midi
        $bg=($h0<=$min and $h3>=$max)?true:false;
      }elseif(!$h2 and !$h3){	// matin seulement
        $bg=($h0<=$min and $h1>=$max)?true:false;
      }elseif(!$h0 and !$h1){	// après midi seulement
        $bg=($h2<=$min and $h3>=$max)?true:false;
      }else{		// matin et après midi avec pause
        $bg=($h0<=$min and $h1>=$max)?true:false;
        $bg=($h2<=$min and $h3>=$max)?true:$bg;
      }

      $bg=$bg?"background:red;":null;
      $text=($bg and !$last)?$min:"&nbsp;";
      $text="&nbsp;";     

      echo "<td style='width:1px;padding:0 1px;$bg'>$text</td>\n";
      $last=$bg?true:false;
    }
  }

  echo "</tr>\n";
}
echo "</table>\n";
echo "</td>\n";

// Liste des absents
echo "<td>";
echo "<table cellspacing='0'>";
$class="tr1";
foreach($absences as $elem){
  $heures=null;
  $debut=null;
  $fin=null;
  if($elem['debut']>"$date 00:00:00"){
    $debut=substr($elem['debut'],-8);
  }
  if($elem['fin']<"$date 23:59:59"){
    $fin=substr($elem['fin'],-8);
  }
  if($debut and $fin){
    $heures=", ".heure2($debut)." - ".heure2($fin);
  }
  elseif($debut){
    $heures=" à partir de ".heure2($debut);
  }
  elseif($fin){
    $heures=" jusqu'à ".heure2($fin);
  }

  $class=$class=="tr1"?"tr2":"tr1";
  echo "<tr class='$class'><td>{$elem['nom']} {$elem['prenom']}</td><td style='padding-left:15px;'>{$elem['motif']}{$heures}</td></tr>\n";
}
echo "</table>\n";
echo "</td></tr>\n";
echo "</table>\n";

function cmp_h0_h2($a,$b){
  if($a['h0']==$b['h0']){
    return($a['h2']>$b['h2']);
  }
  return($a['h0']>$b['h0']);
}
?>

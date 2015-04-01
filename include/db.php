<?php
/*
Planning Biblio, Version 1.9.4
Licence GNU/GPL (version 2 et au dela)
Voir les fichiers README.md et LICENSE
Copyright (C) 2011-2015 - Jérôme Combes

Fichier : include/db.php
Création : mai 2011
Dernière modification : 1er avril 2015
Auteur : Jérôme Combes, jerome@planningbilbio.fr

Description :
Classe db permet d'effectuer des opérations sur la base de données MySQL : 
INSERT, UPDATE, DELETE et autres requetes avec la fonction db::query($requete);

Page appelée par le fichier include/config.php
*/

// pas de $version=acces direct  => redirection vers la page index.php
if(!$version){
  header("Location: ../index.php");
}

include_once "function.php";

class db{
  var $host;
  var $dbname;
  var $dbprefix;
  var $user;
  var $password;
  var $conn;
  var $result;
  var $nb;
  var $error;
  
  function db(){
    $this->host=$GLOBALS['config']['dbhost'];
    $this->dbname=$GLOBALS['config']['dbname'];
    $this->user=$GLOBALS['config']['dbuser'];
    $this->password=$GLOBALS['config']['dbpass'];
    $this->error=false;
    $this->conn=null;
    $this->dbprefix=$GLOBALS['config']['dbprefix'];
  }

  function connect(){
    $this->conn=mysqli_connect($this->host,$this->user,$this->password,$this->dbname);
    if(mysqli_connect_errno($this->conn)){
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
  }
  
  /**
    * fonction de protection des caracteres speciaux
    * @param string $str string à échapper
    * @return string
    * @access public
    */
  public function escapeString($str){
    $this->connect();
    $str=mysqli_real_escape_string($this->conn,$str);
    return $str;
  }

  function query($requete){
    if(!$this->conn){
      $this->connect();
    }

    $req=mysqli_query($this->conn,$requete);

    if(!$req){
      echo "<br/><br/>### ERREUR SQL ###<br/><br/>$requete<br/><br/>";
      echo mysqli_error($this->conn);
      echo "<br/><br/>";
      $this->error=true;
    }
    elseif(strtolower(substr(trim($requete),0,6))=="select" or strtolower(substr(trim($requete),0,4))=="show"){
      $this->nb=mysqli_num_rows($req);
      for($i=0;$i<$this->nb;$i++)
	$this->result[]=mysqli_fetch_array($req);
    }
    $this->disconnect();
  }

  function disconnect(){
    mysqli_close($this->conn);
  }

  function select($table,$infos=null,$where=null,$options=null){
    $infos=$infos?$infos:"*";
    $where=$where?$where:"1";
    $requete="SELECT $infos FROM `{$GLOBALS['config']['dbprefix']}$table` WHERE $where $options";
    $this->query($requete);
  }

  /**
  Fonction permettant de rechercher des infos dans la base de données en utilisant MySQLi
  @param string $table : nom de la table à interroger
  @param string / array infos : valeurs qu'on souhaite récupérer. 
    Si string, nom des champs séparés par des virgules
    Si array : array(champ1, champ2, ...)
  @param string / array where : filtre de recherche. 
    Si string : champ1=valeur1 AND champ2=valeur2 ...,
    Si array : array(champ1=>valeur1, champ2=>valeur2, ...)
  @param string option : permet d'ajouter des options de recherche après where, ex : order by 
  */
  function select2($table,$infos="*",$where="1",$options=null){
    $this->connect();

    if(is_array($infos)){
      $infos=join(",",$infos);
    }

    if(is_array($where)){
      $tmp=array();
      foreach($where as $key => $value){
	$escapedValue=mysqli_real_escape_string($this->conn,$value);
	$tmp[]="$key='$escapedValue'";
      }
      $where=join(" AND ",$tmp);
    }

    $requete="SELECT $infos FROM `{$GLOBALS['config']['dbprefix']}$table` WHERE $where $options";
    $this->query($requete);
  }


  /**
  Fonction permettant de rechercher des infos dans la base de données en utilisant une jointure avec MySQLi
  @param array table1 : tableau contenant le nom de la première table et son index à utiliser pour la jointure
  @param array table2 : tableau contenant le nom de la seconde table et son index à utiliser pour la jointure
  @param array table1Fields : champs de la première table à afficher.
    Les valeurs peuvent être des chaînes de caractères (nom des champs)
    Ou des tableaux ayant pour index "name" => le nom du champ et "as" => l'alias voulu
  @param array table2Fields : champs de la seconde table à afficher.
    Les valeurs peuvent être des chaînes de caractères (nom des champs)
    Ou des tableaux ayant pour index "name" => le nom du champ et "as" => l'alias voulu
  @param array table1Where : Filtre à appliquer sur la première table
  @param array table2Where : Filtre à appliquer sur la première table
  @param string options : permet d'ajouter des options de recherche après where, ex : order by 
  */
  public function selectInnerJoin($table1=array(), $table2=array(), $table1Fields=array(), 
    $table2Fields=array(), $table1Where=array(), $table2Where=array(), $options=null){

    if(empty($table1) or empty($table2)){
      $this->error=true;
      return false;
    }

    // Connection à la base de données
    $this->connect();

    // Initilisation des variables
    $table1Name="{$this->dbprefix}".$table1[0];
    $table2Name="{$this->dbprefix}".$table2[0];
    $table1Index=$table1[1];
    $table2Index=$table2[1];

    // Construction de la requête
    // Valeurs à retourner
    $info=array();
    foreach($table1Fields as $elem){
      if(is_string($elem)){
	$info[]="`$table1Name`.`$elem` AS `$elem`";
      }elseif(is_array($elem)){
	$info[]="`$table1Name`.`{$elem['name']}` AS `{$elem['as']}`";
      }
    }
    foreach($table2Fields as $elem){
      if(is_string($elem)){
	$info[]="`$table2Name`.`$elem` AS `$elem`";
      }elseif(is_array($elem)){
	$info[]="`$table2Name`.`{$elem['name']}` AS `{$elem['as']}`";
      }
    }
    $info=join(", ",$info);

    // Construction de la requête
    // Filtre "Where" et options
    $where=array();
    foreach($table1Where as $key => $value){
      $escapedValue=htmlentities($value,ENT_QUOTES | ENT_IGNORE,"UTF-8",false);
      $escapedValue=mysqli_real_escape_string($this->conn,$escapedValue);
      $where[]="`$table1Name`.`$key`='$escapedValue'";
    }
    foreach($table2Where as $key => $value){
      $escapedValue=htmlentities($value,ENT_QUOTES | ENT_IGNORE,"UTF-8",false);
      $escapedValue=mysqli_real_escape_string($this->conn,$escapedValue);
      $where[]="`$table2Name`.`$key`='$escapedValue'";
    }
    $where=join(" AND ",$where);
  
    // Construction de la requête
    // Assemblage
    $query="SELECT $info FROM `$table1Name` INNER JOIN `$table2Name` ON `$table1Name`.`$table1Index`=`$table2Name`.`$table2Index` ";
    $query.="WHERE $where $options";

    // Execution de la requête
    $this->query($query);
  }

  function update($table,$set,$where=1){
    $requete="UPDATE `{$GLOBALS['config']['dbprefix']}$table` SET $set WHERE $where";
    $this->query($requete);
  }

  function update2($table,$set,$where="1"){
    $this->connect();
    $tmp=array();
    $fields=array_keys($set);
    foreach($fields as $field){
      if(!is_serialized($set[$field]))
	$set[$field]=htmlentities($set[$field],ENT_QUOTES | ENT_IGNORE,"UTF-8",false);
      $set[$field]=mysqli_real_escape_string($this->conn,$set[$field]);
      if(substr($set[$field],0,7)=="CONCAT("){
	$tmp[]="`{$field}`={$set[$field]}";
      }
      else{
	$tmp[]="`{$field}`='{$set[$field]}'";
      }
    }
    $set=join(",",$tmp);
    if(is_array($where)){
      $tmp=array();
      foreach($where as $key => $value){
	$escapedValue=htmlentities($value,ENT_QUOTES | ENT_IGNORE,"UTF-8",false);
	$escapedValue=mysqli_real_escape_string($this->conn,$escapedValue);
	$tmp[]="`$key`='$escapedValue'";
      }
      $where=join(" AND ",$tmp);
    }
    $requete="UPDATE `{$GLOBALS['config']['dbprefix']}$table` SET $set WHERE $where;";

    $req=mysqli_query($this->conn,$requete);
    $this->disconnect();
  }

  function update2latin1($table,$set,$where){
    $this->connect();
    $tmp=array();
    $fields=array_keys($set);
    foreach($fields as $field){
      if(!is_serialized($set[$field]))
	$set[$field]=htmlentities($set[$field],ENT_QUOTES | ENT_IGNORE,"ISO-8859-1",false);
      $set[$field]=mysqli_real_escape_string($this->conn,$set[$field]);
      $tmp[]="`{$field}`='{$set[$field]}'";
    }
    $set=join(",",$tmp);
    $key=array_keys($where);
    $where="`".$key[0]."`='".$where[$key[0]]."'";
    $requete="UPDATE `{$GLOBALS['config']['dbprefix']}$table` SET $set WHERE $where;";
    $req=mysqli_query($this->conn,$requete);
    $this->disconnect();
  }

  function delete($table,$where=1){
    $requete="DELETE FROM `{$GLOBALS['config']['dbprefix']}$table` WHERE $where";
    $this->query($requete);
  }

  function delete2($table,$where="1"){
    $this->connect();

    if(is_array($where)){
      $keys=array_keys($where);
      $tmp=array();
      foreach($keys as $key){
	$value=mysqli_real_escape_string($this->conn,$where[$key]);
	$tmp[]="`".$key."`='$value'";
      }
      $where=join(" AND ",$tmp);
    }

    $requete="DELETE FROM `{$GLOBALS['config']['dbprefix']}$table` WHERE $where";
    $this->query($requete);
  }

  function insert($table,$values,$fields=null){
    $this->connect();
    $fields=$fields?"($fields)":null;
    if(is_array($values)){
      $values=join("),(",$values);
    }
    $requete="INSERT INTO `{$GLOBALS['config']['dbprefix']}$table` $fields VALUES ($values);";
    $req=mysqli_query($this->conn,$requete);
    if(mysqli_error($this->conn)){
      echo mysqli_error($this->conn);
      echo "<br/>".$requete;
    }
    $this->disconnect();
  }

  function insert2($table,$values,$options=null){
    $this->connect();
    $tab=array();
    if(array_key_exists(0,$values)){
      $fields=array_keys($values[0]);
      for($i=0;$i<count($values);$i++){
	foreach($fields as $elem){
	  if(!is_serialized($values[$i][$elem])){
	    $values[$i][$elem]=htmlentities($values[$i][$elem],ENT_QUOTES | ENT_IGNORE,"UTF-8",false);
	    }
	  $values[$i][$elem]=mysqli_real_escape_string($this->conn,$values[$i][$elem]);
	}
      }
      $fields=join(",",$fields);

      foreach($values as $elem){
	$tab[]="'".join("','",$elem)."'";
      }
    }
    else{
      $fields=array_keys($values);
      foreach($fields as $elem){
	if(!is_serialized($values[$elem])){
	  $values[$elem]=htmlentities($values[$elem],ENT_QUOTES | ENT_IGNORE,"UTF-8",false);
	}
	$values[$elem]=mysqli_real_escape_string($this->conn,$values[$elem]);
      }
      $fields=join(",",$fields);
      $tab[]="'".join("','",$values)."'";
    }

    $this->insert($table,$tab,$fields);
  }

}

class dbh{
  var $dbhost;
  var $dbname;
  var $dbuser;
  var $dbpass;
  var $pdo;
  var $stmt;
  var $result;


  function dbh(){
    $this->dbhost=$GLOBALS['config']['dbhost'];
    $this->dbname=$GLOBALS['config']['dbname'];
    $this->dbuser=$GLOBALS['config']['dbuser'];
    $this->dbpass=$GLOBALS['config']['dbpass'];

    $this->pdo=new PDO("mysql:host={$this->dbhost};dbname={$this->dbname}",$this->dbuser,$this->dbpass);
  }

  function exec($sql){
    $this->pdo->exec($sql);
  }

  function prepare($sql){
    $this->stmt=$this->pdo->prepare($sql);
  }

  function execute($data=array()){
    $this->stmt->execute($data);
    $this->result=$this->stmt->fetchAll();
    $this->nb=count($this->result);
  }

  /**
  Fonction permettant de rechercher des infos dans la base de données en utilisant PDO_MySQL
  @param string $table : nom de la table à interroger
  @param string / array infos : valeurs qu'on souhaite récupérer. 
    Si string, nom des champs séparés par des virgules
    Si array : array(champ1, champ2, ...)
  @param string / array where : filtre de recherche. 
    Si string : champ1=valeur1 AND champ2=valeur2 ..., à éviter car les valeurs ne sont pas échappées dans ce cas
    Si array : array(champ1=>valeur1, champ2=>valeur2, ...) à utiliser de préférence car les valeurs sont échapées par PDO_MySQL
  @param string option : permet d'ajouter des options de recherche après where, ex : order by 
  */
  function select($table,$infos="*",$where="1",$options=null){

    if(is_array($infos)){
      $infos=join(",",$infos);
    }

    $data=array();
    if(is_array($where)){
      $fields=array();
      $keys=array_keys($where);
      foreach($keys as $key){
	$data[":$key"]=$where[$key];
	$fields[]="$key=:$key";
      }
      $where=join(" AND ",$fields);
    }

    $requete="SELECT $infos FROM `{$GLOBALS['config']['dbprefix']}$table` WHERE $where $options";
    $this->prepare($requete);
    $this->execute($data);
  }
}

?>
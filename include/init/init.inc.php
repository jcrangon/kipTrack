<?php
//*********** boutique/inc/init.inc.php  ************

//--SESSION
session_start();

//--INCLUDES
include("./class/autoloader/autoloader.php");
include("./include/init/dbdata.inc.php");
include("./lib/pdo-lib.php");
include("./pagetransitions/page-transitions.php");
require_once("./include/functions/fonctions.inc.php");

//--CHEMIN
/**********************/
define("RACINE_SITE","/codiad/workspace/cwampwww/projet1/");
define("RACINE_SERVEUR",$_SERVER["DOCUMENT_ROOT"]);
define("URL_RACINE",Url_racine_projet(RACINE_SITE));


//--LOGGER
/**********************/
$CONFIG["log"]["innerlog_activate"]=0;
$CONFIG["log"]["mainlog_activate"]=0;
$CONFIG["log"]["php_error_log"]=0;
$log=new phplogger("./class",1,$CONFIG["log"]["innerlog_activate"]);
$log->createglobalref();
$log->activate();
if($CONFIG["log"]["mainlog_activate"]==0){$log->quietmode();}
if($CONFIG["log"]["php_error_log"]==0){$log->php_quietmode();}
$log->start();
$log->info("Dans include/init.inc.php","");


//--ERRCODES
/**********************/
$errCodes=array(
	"getPDO_returns_Fatal"   => "Error FE001",
	"getPDO_returns_NULL"   => "Error FE002",
	"resultat_execute_fails" => "Error FE003",
	);
	

//--LOG CURRENT FILE
/**********************/
$tab_url=explode("/",$_SERVER['REQUEST_URI']);
$fichier_actuel=$tab_url[sizeof($tab_url)-1];
$log->info("Appel de ***************************** ".$fichier_actuel,"");


//--CONNEXION_BDD
/**********************/
if(!$pdo=getPDO($dbdata)){
	$log->info("Retour dans include/init/init.inc.php","");
	$log->error("Fatal Error dans fonction: getPDO()","");
	$log->stop();
	$log->kill();
	DIE($errCodes["getPDO_returns_Fatal"]);
}
if(is_numeric($pdo) && $pdo<0){
	$log->info("Retour dans include/init/init.inc.php","");
	$log->error("Erreur de connexion dans fonction: getPDO()","");
	$log->stop();
	$log->kill();
	DIE($errCodes["getPDO_returns_NULL"]);
}
$log->info("Retour dans include/init/init.inc.php","");
$log->info("Objet PDO créé avec succes, \$pdo",$pdo);


//--VARIABLES
/**********************/
$error=array();
$success=array();
$page="";
$notice="";
$modify=false;
define("REQUIRED",true);

$GET_Data_Avail=false;
$POST_Data_Avail=false;


//--VERIFICATIONS
/**********************/
if(isset($_SESSION["user"])){
	$log->info("\$_SESSION['user']",$_SESSION["user"]);
}


//--NETTOYAGE DES DONNEES ENTRANTES
/******************************************/
$log->info("vérification des données entrantes \$_GET","");
$GET_Data_Avail=checkIncomingData($_GET);
	
$log->info("vérification des données entrantes \$_POST","");
$POST_Data_Avail=checkIncomingData($_POST);


?>
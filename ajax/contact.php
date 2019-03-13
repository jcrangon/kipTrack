<?php
/* ajax/chgpwd.php */
session_start();
include("../class/autoloader/autoloader.php");
include("../include/init/dbdata.inc.php");
include("../lib/pdo-lib.php");
require_once("../include/functions/fonctions.inc.php");
require '../mailer/PHPMailerAutoload.php';

//--CHEMIN
/**********************/
define("RACINE_SITE","/codiad/workspace/cwampwww/projet1/");
define("RACINE_SERVEUR",$_SERVER["DOCUMENT_ROOT"]);

//--LOGGER
/**********************/
$CONFIG["log"]["innerlog_activate"]=1;
$CONFIG["log"]["mainlog_activate"]=1;
$CONFIG["log"]["php_error_log"]=1;
$log=new phplogger("../class",1,$CONFIG["log"]["innerlog_activate"]);
$log->createglobalref();
$log->activate();
if($CONFIG["log"]["mainlog_activate"]==0){$log->quietmode();}
if($CONFIG["log"]["php_error_log"]==0){$log->php_quietmode();}
$log->start();
$log->info("Dans include/init.inc.php","");


//--ERRCODES
/**********************/
$errCodes=array(
	"getPDO_returns_False"   => "Error FE001",
	"getPDO_returns_Fatal"   => "Error FE002",
	"resultat_execute_fails" => "Error FE004",
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
	DIE($errCodes["getPDO_returns_False"]);
}
$log->info("Retour dans ajax/chgpwd.php", "");
$log->info("Objet PDO créé avec succes, \$pdo",$pdo);



//--VARIABLES
/**********************/
$error=array();
$success=array();
$page="";
$notice="";
define("REQUIRED",true);

$GET_Data_Avail=false;
$POST_Data_Avail=false;

if(isset($_SESSION["user"])){
	$log->info("\$_SESSION['user']",$_SESSION["user"]);
}

$log->info("vérification des données entrantes \$_GET & \$_POST","");
if(isset($_GET) && !empty($_GET)){
	$_GET=cleanIncomingData($_GET);
	$GET_Data_Avail=true;
	$log->info("Nettoyage des données entrantes \$_GET",$_GET);
}
else{
	$log->info("pas de données entrantes \$_GET",$_GET);
}

if(isset($_POST) && !empty($_POST)){
	$_POST=cleanIncomingData($_POST);
	$POST_Data_Avail=true;
	$log->info("Nettoyage des données entrantes \$_POST",$_POST);
}
else{
	$log->info("pas de données entrantes \$_POST",$_POST);
}

$smtp_conf=array(
	"auth"=>"true", 
	"host"=>"xxx.fr", 
	"user"=>"jc.rangon@xxxx.fr", 
	"userpwd"=>"xxxxx",
	"secure"=>"",       
	"port"=>"587",  
	"noreply"=>"no-reply@xxxx.fr",
	"noreplyMaster" => "WebMaster@xxx.fr",
	"addresseTO" => "",
	"nameTO"    => "",
);

//**************  MAIN CODE
$page="ajax contact";

if($POST_Data_Avail){
	$verif=validate_lastname($_POST["nom"], $regex["nom"], true, 3, 60);
	if($verif["error"]!="none"){
		$data["status"]="error";
		$data["field"]="nom";
		$data["notice"]=$verif["error"];
		
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	$verif=validate_email($_POST["email"], $regex["email"], true);
	if($verif["error"]!="none"){
		$data["status"]="error";
		$data["field"]="nom";
		$data["notice"]=$verif["error"];
		
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	$verif=validate_phone1($_POST["phone"], $regex["tel"], false);
	if($verif["error"]!="none"){
		$data["status"]="error";
		$data["field"]="nom";
		$data["notice"]=$verif["error"];
		
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	

	$msg=makeMsg($_POST['nom'],$_POST["email"],$_POST["msg"],$_POST["phone"]);
	
	$mail=new PHPMailer();
	$log->info("Sending mail Using SMTP ...","");
	
	if(!$sendresult=mailMeViaSMPT($mail,"Kiptrak Contact Form",$msg,$smtp_conf)){
		$data["status"]="tempUnavail";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	$data["status"]="ok";
	$data["field"]="none";
	$data["notice"]="Message Correctement Envoyé. Nous ne manquerons pas de vous répondre dans les plus brefs délais";
	
	closeCnx($pdo);
	$log->info("destruction de l'objet PDO", $pdo);
	$log->stop();
	$log->kill();
	echo json_encode($data);
	exit();
	
}

/**********************/
closeCnx($pdo);
$log->info("destruction de l'objet PDO", $pdo);
$log->stop();
$log->kill();
?>
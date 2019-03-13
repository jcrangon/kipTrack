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
define("PORT",":440");
define("RACINE_SERVEUR",$_SERVER["DOCUMENT_ROOT"]);
define("URL_RACINE",Url_racine_projet(PORT.RACINE_SITE));

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

//**************  MAIN CODE
$page="ajax chgpwd";

if(!userISConnected()){
	$data["status"]="sessionExpire";
	$data["payload"]="";
	closeCnx($pdo);
	$log->info("destruction de l'objet PDO", $pdo);
	$log->stop();
	$log->kill();
	echo json_encode($data);
	exit();
}

if($POST_Data_Avail){
	extract($_POST);
	$mdp=$ancien.$_SESSION["user"]["pseudo"];
	$req="SELECT password FROM qr_user WHERE id=:id";
	try{
		$resultat=$pdo->prepare($req);
		$resultat->bindParam(":id",$_SESSION["user"]["id"],PDO::PARAM_INT);
		$resultat->execute();
	}
	catch(PDOException $e){
		$log->error("",$e->xdebug_message);
		$data["status"]="error";
		$data["payload"]="Operation impossible";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	catch(Exception $e){
		$log->error("",$e->xdebug_message);
		$data["status"]="error";
		$data["payload"]="Operation impossible";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	$pass=$resultat->fetch();
	$pass=$pass["password"];
	$log->info("pass",$pass);
	
	if(!Dcrypt($mdp,$pass)){
		$log->error("le mot de passe fourni ne correspond pas","");
		$data["status"]="badoldpwd";
		$data["payload"]="";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	$verif_mdp=validate_password($nouv1,$regex["mdp"],REQUIRED,8,15);
	if($verif_mdp["error"]!=="none"){
		$log->error("nouv1 ne passe pas le test","");
		$data["status"]="error";
		$data["payload"]=$verif_mdp["error"];
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	$verif_mdp=validate_password($nouv2,$regex["mdp"],REQUIRED,8,15);
	if($verif_mdp["error"]!=="none"){
		$log->error("nouv2 ne passe pas le test","");
		$data["status"]="error";
		$data["payload"]=$verif_mdp["error"];
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	if($nouv1!==$nouv2){
		$log->error("les mots de passe ne sont pas identiques","");
		$data["status"]="error";
		$data["payload"]="Les mots de passe ne sont pas identiques!";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	$mdp=Bcrypt($nouv1.$_SESSION["user"]["pseudo"]);
	$req="UPDATE qr_user SET password=:mdp WHERE id=:id";
	try{
		$resultat=$pdo->prepare($req);
		$resultat->bindParam(":mdp",$mdp,PDO::PARAM_STR);
		$resultat->bindParam(":id",$_SESSION["user"]["id"]);
		$resultat->execute();
	}
	catch(PDOException $e){
		$log->error("",$e->xdebug_message);
		$data["status"]="error";
		$data["payload"]="Operation impossible";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	catch(Exception $e){
		$log->error("",$e->xdebug_message);
		$data["status"]="error";
		$data["payload"]="Operation impossible";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	$data["status"]="ok";
	$data["payload"]="Mot de passe correctement mis à jour";
	closeCnx($pdo);
	$log->info("destruction de l'objet PDO", $pdo);
	$log->stop();
	$log->kill();
	echo json_encode($data);
	exit();
}

$data["status"]="error";
$data["payload"]="no-data";
/**********************/
closeCnx($pdo);
$log->info("destruction de l'objet PDO", $pdo);
$log->stop();
$log->kill();
echo json_encode($data);
exit();
?>
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

$smtp_conf=array(
	"auth"=>"true", 
	"host"=>"devnetx.hd.free.fr", 
	"user"=>"jc.rangon@devnetx.hd.free.fr", 
	"userpwd"=>"secteur5",
	"secure"=>"",       
	"port"=>"587",  
	"noreply"=>"no-reply@devnetx.hd.free.fr",
	"noreplyMaster" => "WebMaster@devnetx.hd.free.fr",
	"addresseTO" => "",
	"nameTO"    => "",
);

//**************  MAIN CODE
$page="ajax fgtpwd";


if($POST_Data_Avail){
	$log->info("verification de l'email","");
	extract($_POST);
	$verif_email=validate_email($email,$regex["email"],REQUIRED);
	if($verif_email["error"]!=="none"){
		$log->warning("Erreur",$verif_email["error"]);
		$data["status"]="error";
		$data["notice"]=$verif_email["error"];
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	$log->info("Succes","");
	$req="SELECT id,nom,prenom FROM qr_user WHERE email=:email";
	
	try{
		$resultat=$pdo->prepare($req);
		$resultat->bindParam(":email",$email,PDO::PARAM_STR);
		$resultat->execute();
	}
	catch(PDOException $e){
		$log->error("",$e->xdebug_message);
		$data["status"]="error";
		$data["notice"]="Operation impossible";
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
		$data["notice"]="Operation impossible";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	if($resultat->rowCount()==0){
		$data["status"]="error";
		$data["notice"]="Email inconnu";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	$user_data=$resultat->fetch();
	$userid=$user_data["id"];
	$usernom=$user_data["nom"];
	$userprenom=$user_data["prenom"];
	
	$log->info("user_data",$user_data);
	
	$ccode=genmdp(60);
	$hcode=Bcrypt($ccode);
	
	$req="UPDATE qr_user SET hcode=:hcode, validite=ADDDATE(NOW(), INTERVAL 48 HOUR) WHERE id=:id";
	$resultat=$pdo->prepare($req);
	$resultat->bindParam(":hcode",$hcode,PDO::PARAM_STR);
	$resultat->bindParam(":id",$userid,PDO::PARAM_STR);
	try{
		$resultat->execute();
	}
	catch(PDOException $e){
		$log->error("",$e->xdebug_message);
		$data["status"]="error";
		$data["notice"]="Operation impossible";
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
		$data["notice"]="Operation impossible";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	$msg=makeForgottenEmailMsg($userid,$email,$ccode,$usernom,$userprenom,URL_RACINE);
	$mailer=new PHPMailer();
	$smtp_conf["addresseTO"]=$email;
	$smtp_conf["nameTO"]=$userprenom." ".$usernom;
	
	$log->info("smtp conf",$smtp_conf);
	
	$log->info("Sending mail Using SMTP ...","");
	
	if(!$sendresult=mailMeViaSMPT($mailer,"Kiptrak Password Reset",$msg,$smtp_conf)){
		$data["status"]="warning";
		$data["notice"]="Service temporairement suspendu. Veuillez ré-essayer dans quelques minutes";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	$data["status"]="ok";
	$data["notice"]="Email Correctement Envoyé.";
	
	closeCnx($pdo);
	$log->info("destruction de l'objet PDO", $pdo);
	$log->stop();
	$log->kill();
	echo json_encode($data);
	exit();
	
	
	
}
$data["status"]="error";
$data["notice"]="no-data";
/**********************/
closeCnx($pdo);
$log->info("destruction de l'objet PDO", $pdo);
$log->stop();
$log->kill();
echo json_encode($data);
exit();
?>
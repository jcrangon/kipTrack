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
$page="ajax chgpic";

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

		
//traitement de la photo
$log->info("Traitement de la photo","");
$photo_path=RACINE_SERVEUR.RACINE_SITE."assets/img/";
$photo_bdd=$_SESSION["user"]["photo"];
	
$log->info("$ FILE",$_FILES);
	
if(!empty($_FILES["photo"]["name"])){
	$photo_bdd=time().'_'.rand(1,9999).$_FILES['photo']['name'];
	$photo_user=$photo_path."user-".$_SESSION["user"]["id"]."-300x-".$photo_bdd;
	$authorizedImages=array("image/jpg","image/jpeg","image/png","image/gif");
	
	$log->info("photo bdd",$photo_bdd);
	$log->info("photo",$_FILES['photo']);
	
	if($_FILES['photo']['size']>2000000){
		$log->error("fichier photo trop volumineux","");
		$data["status"]="error";
		$data["fieldID"]="pic";
		$data["payload"]="Le fichier doit être inférieur à 2Mo.";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	elseif(!in_array($_FILES["photo"]["type"],$authorizedImages)){
		$log->error("Format photo non autorisé","");
		$data["status"]="error";
		$data["fieldID"]="pic";
		$data["payload"]="Le fichier doit être au format jpg, jpeg, png ou gif.";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	elseif(!copy($_FILES["photo"]["tmp_name"], $photo_user)){
		$log->error("Erreur lors de l'enregistrement de la photo de profil","");
		$data["status"]="error";
		$data["fieldID"]="pic";
		$data["payload"]="Erreur lors de l'enregistrement de la photo de profil.";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	elseif(!file_exists($photo_user)){
		$log->error("Erreur lors de la creation de la photo de profil","");
		$data["status"]="error";
		$data["fieldID"]="pic";
		$data["payload"]="Erreur lors de la creation de la photo de profil.";
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	$thumb_user=$photo_path."user-".$_SESSION["user"]["id"]."-thumb-150x-".$photo_bdd;

	$log->info("thumb_user",$thumb_user);
	
	if(!file_exists($thumb_user)){
		if(!make_thumb($photo_user,$thumb_user,150)){
			$log->error("Erreur lors de la creation du thumbnail de profil","");
			$data["status"]="error";
			$data["fieldID"]="pic";
			$data["payload"]="Erreur lors de la creation du thumbnail de profil.";
			closeCnx($pdo);
			$log->info("destruction de l'objet PDO", $pdo);
			$log->stop();
			$log->kill();
			echo json_encode($data);
			exit();
		}
		$log->info("thumb_user",$thumb_user);
	}
}

if($photo_bdd!=$_SESSION["user"]["photo"]){
	$photo="user-".$_SESSION["user"]["id"]."-300x-".$photo_bdd;
}
else{
	$photo=$photo_bdd;
}

$req="UPDATE qr_user SET photo=:photo WHERE id=:id";

try{
	$resultat=$pdo->prepare($req);
	$resultat->bindParam(":photo",$photo,PDO::PARAM_STR);
	$resultat->bindParam(":id",$_SESSION["user"]["id"],PDO::PARAM_INT);
	$resultat->execute();
}
catch(PDOException $e){
	$log->error("",$e->xdebug_message);
	$data["status"]="errorSQL";
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
	$data["status"]="errorSQL";
	$data["payload"]="Operation impossible";
	closeCnx($pdo);
	$log->info("destruction de l'objet PDO", $pdo);
	$log->stop();
	$log->kill();
	echo json_encode($data);
	exit();
}

$_SESSION["user"]["photo"]=$photo;

$data["status"]="ok";
$data["payload"]="";
closeCnx($pdo);
$log->info("destruction de l'objet PDO", $pdo);
$log->stop();
$log->kill();
echo json_encode($data);
exit();

?>
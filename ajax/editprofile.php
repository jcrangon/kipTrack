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
$page="ajax editprofile";

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
	
	// verification de pseudo
	$log->info("Verification de pseudo","");
	$verif_pseudo=validate_pseudo($pseudo,$regex["pseudo"],REQUIRED,3,30);
	
	if($verif_pseudo["error"]!=="none"){
		$log->error("pseudo ne passe pas le test","");
		$data["status"]="error";
		$data["fieldID"]="ident";
		$data["payload"]=$verif_mdp["error"];
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	else{
		try{
			$log->info("Verification de l'unicité de pseudo","");
			$resultat=$pdo->prepare("SELECT * FROM qr_user WHERE pseudo=:pseudo AND id<>:id");
			$resultat->bindParam(':pseudo', $pseudo,PDO::PARAM_STR);
			$resultat->bindParam(':id', $_SESSION["user"]["id"], PDO::PARAM_STR);
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
		
		if($resultat->rowCount()!=0){ // le pseudo existe deja en base de donnée
			$log->error("pseudo non disponible","");
			$data["status"]="error";
			$data["fieldID"]="ident";
			$data["payload"]="Pseudo non disponible";
			closeCnx($pdo);
			$log->info("destruction de l'objet PDO", $pdo);
			$log->stop();
			$log->kill();
			echo json_encode($data);
			exit();
		}
	}
	
	// verification du nom
	$log->info("Verification du nom","");
	$verif_nom=validate_lastname($nom,$regex["nom"],REQUIRED,3,30);
	if($verif_nom["error"]!=="none"){
		$log->error("nom ne passe pas le test","");
		$data["status"]="error";
		$data["fieldID"]="lname";
		$data["payload"]=$verif_mdp["error"];
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	// verification du prenom
	$log->info("Verification du prénom","");
	$verif_prenom=validate_firstname($prenom,$regex["prenom"],REQUIRED,3,30);
	if($verif_prenom["error"]!=="none"){
		$log->error("prenom ne passe pas le test","");
		$data["status"]="error";
		$data["fieldID"]="fname";
		$data["payload"]=$verif_mdp["error"];
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	
	// verification de l'email
    $log->info("Verification de l'email","");
    $verif_email=validate_email($email,$regex["email"],REQUIRED);
	if($verif_email["error"]!=="none"){
		$log->error("email ne passe pas le test","");
		$data["status"]="error";
		$data["fieldID"]="mail";
		$data["payload"]=$verif_mdp["error"];
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	else{
		$log->info("Verification de l'unicité de email","");
        try{
        	$req="SELECT id FROM qr_user WHERE email=:email AND id<>:id";
	        $resultat=$pdo->prepare($req);
	        $resultat->bindParam(":email",$_POST['email'],PDO::PARAM_STR);
	        $resultat->bindParam(':id', $_SESSION["user"]["id"], PDO::PARAM_STR);
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
		
        if($resultat->rowCount()!==0){
        	$log->error("email non disponible","");
			$data["status"]="error";
			$data["fieldID"]="mail";
			$data["payload"]="email non disponible";
			closeCnx($pdo);
			$log->info("destruction de l'objet PDO", $pdo);
			$log->stop();
			$log->kill();
			echo json_encode($data);
			exit();
        }
    }
	
	// verification de date de naissance
    $log->info("Verification de la date de naissance","");
    $verif_birthdate=validate_birthdate($dob,$regex["date_de_naissance"],REQUIRED);
	if($verif_birthdate["error"]!=="none"){
		$log->error("email ne passe pas le test","");
		$data["status"]="error";
		$data["fieldID"]="mail";
		$data["payload"]=$verif_birthdate["error"];
		closeCnx($pdo);
		$log->info("destruction de l'objet PDO", $pdo);
		$log->stop();
		$log->kill();
		echo json_encode($data);
		exit();
	}
	

	$req="UPDATE qr_user SET pseudo=:pseudo, nom=:nom, prenom=:prenom, email=:email, password=:pwd, date_de_naissance=:dob WHERE id=:id";
	
	$log->info("pwd",$_SESSION["thread"]["title"].$pseudo);
	
	$pwd=Bcrypt($_SESSION["thread"]["title"].$pseudo);
	try{
		$resultat=$pdo->prepare($req);
		$resultat->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
		$resultat->bindParam(":nom", $nom, PDO::PARAM_STR);
		$resultat->bindParam(":prenom", $prenom, PDO::PARAM_STR);
		$resultat->bindParam(":email", $email, PDO::PARAM_STR);
		$resultat->bindParam(":pwd", $pwd, PDO::PARAM_STR);
		$resultat->bindParam(":dob", $dob, PDO::PARAM_STR);
		$resultat->bindParam(":id", $_SESSION["user"]["id"], PDO::PARAM_INT);
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
	
	$_SESSION["user"]["pseudo"]=$pseudo;
	$_SESSION["user"]["nom"]=$nom;
	$_SESSION["user"]["prenom"]=$prenom;
	$_SESSION["user"]["email"]=$email;
	$_SESSION["user"]["date_de_naissance"]=$dob;
	$data["status"]="ok";
	$data["payload"]="";
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
exit();
?>
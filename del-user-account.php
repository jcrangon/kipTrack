<?php
// deconnexion.php
require_once('include/init/init.inc.php');
$log->info("mise a zero du status utilisateur","");
try{
	$req="UPDATE qr_user SET status=0 WHERE id=:id";
	$resultat=$pdo->prepare($req);
	$resultat->bindParam(":id",$_SESSION["user"]["id"],PDO::PARAM_INT);
	$resultat->execute();
}
catch(PDOException $e){
	$log->fatal("erreur PDO1",$e->xdebug_message);
	die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px;text-align:center;'>Une erreur est survenue. Veuillez recharger la page.</div>");
}
catch(Exception $e){
	$log->fatal("erreur PDO2",$e->xdebug_message);
	die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px'>Une erreur est survenue. Veuillez recharger la page.</div>");
}
$log->info("Succes","");
$log->info("Desturction des tableaux de session","");
if(isset($_SESSION["user"])){
	unset($_SESSION["user"]);
}
if(isset($_SESSION["settings"])){
	unset($_SESSION["settings"]);
}
$log->info("Redirection vers accueil.php","");
/**********************/
closeCnx($pdo);
$log->info("destruction de l'objet PDO", $pdo);
$log->stop();
$log->kill();
header("location:./accueil.php");
exit();
?>
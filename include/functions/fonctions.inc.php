<?php
function cleanIncomingData($post){
	if(isset($GLOBALS["loggerref"])){$logger=$GLOBALS["loggerref"];$log=true;}else{$log=false;}
	if($log){$logger->info("** Dans function cleanIncomingData()","");}
	$tab=array();
	foreach($post as $key=>$value){
		$tab[$key]=trim(stripslashes(strip_tags($value, ENT_QUOTES)));
	}
	return $tab;
}

function checkIncomingData(&$superGlog){
	if(isset($GLOBALS["loggerref"])){$logger=$GLOBALS["loggerref"];$log=true;}else{$log=false;}
	if($log){$logger->info("** Dans function checkIncomingData()","");}
	if(isset($superGlog) && !empty($superGlog)){
		$superGlog=cleanIncomingData($superGlog);
		if($log){$logger->info("Nettoyage des données entrantes...ok","");}
		return true;
	}
	else{
		if($log){$logger->info("pas de données entrantes","");}
		return false;
	}
}

function debug($var){
	echo "<div style='background:#".rand(111111, 999999).";color:white;padding:5px;'>";
	$trace=debug_backtrace();// retourne  un array contenant des infos surla ligne executée.
	$info=array_shift($trace); // extrait la premiere valeur d'un array
	
	echo "Le debug a été demandé dans le fichier ".$info["file"]." à la ligne ".$info["line"]."<hr/>";
	echo "<pre>";
	print_r($var);
	echo "</pre>";
	echo "</div>";
}

function Bcrypt($pwd){
    $options = [
		'cost' => 12,
		];
	$mot_de_passe=password_hash($pwd, PASSWORD_BCRYPT, $options);
    return $mot_de_passe;
}

function Dcrypt($mdpclair,$mdpstocke){
	if(isset($GLOBALS["loggerref"])){$logger=$GLOBALS["loggerref"];$log=true;}else{$log=false;}
	if($log){$logger->info("** Dans function checkpass()","");}
	if (password_verify($mdpclair, $mdpstocke)) {
		if($log){$logger->info("-- Valeur de retour : TRUE","");}
		return true;
	}
	else {
		if($log){$logger->info("-- Valeur de retour : FALSE","");}
		return false;
	}
}

function genmdp($nb_caractere)
{
	$mot_de_passe = "";

	$chaine = "abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ023456789";
	$longeur_chaine = strlen($chaine);

	for($i = 1; $i <= $nb_caractere; $i++)
	{
		$place_aleatoire = mt_rand(0,($longeur_chaine-1));
		$mot_de_passe .= $chaine[$place_aleatoire];
	}

	return $mot_de_passe;
}

function setperiod($periode){
	switch($periode){
		case 1:
			 $datereq="BETWEEN  DATE_SUB(CURDATE(),INTERVAL 1 MONTH) AND CURDATE()";
		break;
		
		case 2:
			 $datereq="BETWEEN DATE_SUB(CURDATE(),INTERVAL 3 MONTH) AND CURDATE()";
		break;
		
		case 3:
			 $datereq="BETWEEN DATE_SUB(CURDATE(),INTERVAL 12 MONTH) AND CURDATE()";
		break;
		default:
			 $datereq="BETWEEN  DATE_SUB(CURDATE(),INTERVAL 1 MONTH) AND CURDATE()";
	}
	return $datereq;
}

function frenchDateTime($mysqldate,$option){
	if($option==1){
		setlocale(LC_TIME, 'fr_FR.utf8','fra');
		$b=utf8_encode(strftime("%a %d %b %H:%M:%S",strtotime($mysqldate)));
		return $b;
	}
	
	if($option==2){
		setlocale(LC_TIME, 'fr_FR.utf8','fra');
		$b=utf8_encode(strftime("%a %d %b",strtotime($mysqldate)));
		return $b;
	}
}

function mysqltofrenchdate($mysqldate){
	return date("d-m-Y",strtotime($mysqldate));
}

function frenchtomysqldate($frenchdate){
	return date("Y-m-d",strtotime($mysqldate));
}

function mysqlnow($option){
	if (($option==1)) {
		$msqldate=date("Y-m-d");
	}

	if (($option==2)) {
		$msqldate=date("Y-m-d H:i:s");
	}

	return $msqldate;
}

function userISConnected(){
	if(isset($_SESSION["user"]) && !empty($_SESSION["user"])){
		return true;
	}
	else{
		if(isset($_SESSION["user"])){
			unset($_SESSION["user"]);
		}
		return false;
	}
}

function userISAdmin(){
	if($_SESSION["user"]["role"]=="1"){
		return true;
	}
	else{
		return false;
	}
}

// Fonction pour ajouter un produit au panier
function ajouterProduit($id_produit, $quantite, $photo, $titre, $prix, $categorie){
	if(!isset($_SESSION['panier'])){
		$_SESSION['panier'] = array();
		// $_SESSION['panier']['id_produit'] = array();
		// $_SESSION['panier']['titre'] = array();
		// $_SESSION['panier']['photo'] = array();
		// $_SESSION['panier']['prix'] = array();
		// $_SESSION['panier']['quantite'] = array();
	}
	else{
		$position = array_search($id_produit, $_SESSION['panier']['id_produit']);
		// Si le produit existe déjà dans le panier, $position va contenir un chiffre (0, 1, 2...), ou alors false si le produit n'est pas déjà dans le panier. 
	}
	
	if(isset($position) && $position !==  false){
		// Si le produit existe dans le panier, on va dans le tableau qui stocke les quantité pour lui ajouter la nouvelle quantité
		$_SESSION['panier']['quantite'][$position] += $quantite;
	}
	else{
		// Le produit n'était pas dans le panier
		$_SESSION['panier']['titre'][] = $titre;
		$_SESSION['panier']['categorie'][] = $categorie;
		$_SESSION['panier']['id_produit'][] = $id_produit;
		$_SESSION['panier']['quantite'][] = $quantite;
		$_SESSION['panier']['photo'][] = $photo;
		$_SESSION['panier']['prix'][] = $prix;
	}
}

function totalArtPanier(){
	$i=0;
	if(isset($_SESSION['panier']) && !empty($_SESSION['panier'])){
		foreach($_SESSION['panier']['quantite'] as $qte)
		$i+=$qte;
	}
	return $i;
}

function make_thumb($src, $dest, $desired_width) {

	/* read the source image */
	$ext = pathinfo($src, PATHINFO_EXTENSION);
	switch(strtolower($ext)){
		case "jpg":
			$source_image = imagecreatefromjpeg($src);
		break;
		case "jpeg":
			$source_image = imagecreatefromjpeg($src);
		break;
		case "png":
			$source_image = imagecreatefrompng($src);
		break;
		case "gif":
			$source_image = imagecreatefromgif($src);
		break;
	}
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	
	/* find the "desired height" of this thumbnail, relative to the desired width  */
	$desired_height = floor($height * ($desired_width / $width));
	
	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
	
	/* copy source image at a resized size */
	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
	
	/* create the physical thumbnail image to its destination */
	switch(strtolower($ext)){
		case "jpg":
			imagejpeg($virtual_image, $dest);
		break;
		case "jpeg":
			imagejpeg($virtual_image, $dest);
		break;
		case "png":
			imagepng($virtual_image, $dest);
		break;
		case "gif":
			imagegif($virtual_image, $dest);
		break;
	}
	return true;
}

function getMailExtension($email){
	$result=substr(substr($email,strpos($email,"@")),1,strpos(substr($email,strpos($email,"@")),".")-1);
	return $result;
}

function getDateSelectData($minAge){
	$tab["date"]=array();
	$tab["month"]=array();
	$tab["year"]=array();
	for($i=1;$i<32;$i++){
		if($i<10){
			$tab["date"]["0".strval($i)]="0".strval($i);
		}
		else{
			$tab["date"][strval($i)]=strval($i);
		}
		
	}
	
	$tab["month"]=array(
		"01" => "janvier",
		"02" => "février",
		"03" => "mars",
		"04" => "avril",
		"05" => "mai",
		"06" => "juin",
		"07" => "juillet",
		"08" => "août",
		"09" => "septembre",
		"10" => "octobre",
		"11" => "novembre",
		"12" => "décembre",
		);
	
	$endYear=intval(date("Y"))-$minAge+1;
	$startYear=$endYear-100;
	for($i=$endYear;$i>=$startYear;$i--){
		$tab["year"][strval($i)]=strval($i);
	}
	
	return $tab;
}

function isForbiddenEmail($email,$extensionList=array()){
	if(isset($GLOBALS["loggerref"])){$logger=$GLOBALS["loggerref"];$log=true;}else{$log=false;}
	if($log){$logger->info("** Dans function isForbiddenEmail()","");}
	if(!empty($list)){
		$forbiddenExt=$extensionList;
	}
	else{
		$forbiddenExt=array(
		"yopmail",
		"mailinator",
		"mail"
		);
	}
	$ext=getMailExtension($email);
	if($log){$logger->info("\$ext",$ext);}
	if($log){$logger->info("isforbidden?",in_array($ext,$forbiddenExt));}
	if(in_array($ext,$forbiddenExt)){
		return true;
	}
	else{
		return false;
	}
}

function nl2br2($string) {
	$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
	return $string;
}

$regex=array(
	"pseudo"                 => "/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ\.\-\_]{3,}$/",
	"nom"                    => "/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}+([\'\s-]{0,1}[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}){0,4}$/",
	"prenom"                 => "/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{3,30}+([\'\s-]{0,1}[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}){0,4}$/",
	"email"                  => "/[A-Za-z0-9._%+-]{1,}@[a-zA-Z]{1,}([.]{1}[a-zA-Z]{2,}|[.]{1}[a-zA-Z]{2,}[.]{1}[a-zA-Z]{2,})/",
	"mdp"                    => "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/",
	"adresse"                => "/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ\.\'\-\s]{3,100}$/",
	"code_postal"            => "/^[0-9]{5}$/",
	"ville"                  => "/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ\.\'\-\s]{3,60}$/",
	"date_de_naissance"      => "/^[0-9]{4}([\-]{1}[0-9]{2}){2}$/",
	"tel"                    =>"/^[0-9]{10}$/",
	"categorie"              =>"/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}+([\.\'\s-]{0,1}[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{0,30}[\.\-]{0,1}){0,4}$/",
	"compte"                 =>"/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}+([\.\'\s-]{0,1}[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{0,30}[\.\-]{0,1}){0,4}$/",
	"montant"                =>"/^[0-9.]{1,8}+$/",
	);

function validate_lastname($name, $regex, $required=true, $min=3, $max=30){
	if($required && empty($name)){
		return array("error"=>"Veuillez Renseigner le champs 'Nom'.");
	}
	if(!empty($name) && (strlen($name)<$min || strlen($name)>$max)){
		return array("error"=>"Le champs 'Nom' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les tirets '-' ainsi que les apostrophes (') sont autorisées.");
	}
	if(!empty($name) && !preg_match($regex,$name)){
		return array("error"=>"Le champs 'Nom' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les tirets '-' ainsi que les apostrophes (') sont autorisées.");
	}
	return array("error"=>"none");
}

function validate_firstname($name, $regex, $required=true, $min=3, $max=30){
	if($required && empty($name)){
		return array("error"=>"Veuillez Renseigner le champs 'Prénom'.");
	}
	if(!empty($name) && (strlen($name)<$min || strlen($name)>$max)){
		return array("error"=>"Le champs 'Prénom' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les tirets '-' ainsi que les apostrophes (') sont autorisées.");
	}
	if(!empty($name) && !preg_match($regex,$name)){
			return array("error"=>"Le champs 'Prénom' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les tirets '-' ainsi que les apostrophes (') sont autorisées.");
	}
	return array("error"=>"none");
}

function validate_pseudo($pseudo, $regex, $required=true, $min=3, $max=60){
	if($required && empty($pseudo)){
		return array("error"=>"Veuillez Renseigner le champs 'Pseudo'.");
	}
	if(!empty($pseudo) && (strlen($pseudo)<$min || strlen($pseudo)>$max)){
		return array("error"=>"Le champs 'Pseudo' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les tirets '-', underscores '_' ainsi que les points '.' sont autorisés.");
	}
	if(!empty($pseudo) && !preg_match($regex,$pseudo)){
		return array("error"=>"Le champs 'Pseudo' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces sont interdits. Les tirets '-', underscores '_' ainsi que les points '.' sont autorisés.");
	}
	return array("error"=>"none");
}

function validate_email($email, $regex, $required=true){
	if($required && empty($email)){
		return array("error"=>"Veuillez Renseigner le champs 'Email'.");
	}
	if(!empty($email) && !preg_match($regex,$email)){
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			return array("error"=>"Veuillez renseigner un email valide.");
		}
	}
	if(!empty($email) && isForbiddenEmail($email)){
		return array("error"=>"Adresse email non autorisée.");
	}
	return array("error"=>"none");
}

function validate_password($pwd, $regex, $required=true, $min=8, $max=15){
	if($required && empty($pwd)){
		return array("error"=>"Veuillez Renseigner le champs 'Mot de passe'.");
	}
	if(!empty($pwd) && (strlen($pwd)<$min || strlen($pwd)>$max)){
		return array("error"=>"Le champs 'Mot de Passe' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Il doit contenir au moins une lettre MAJUSCULE, une lettre MINUSCULE, un CHIFFRE et un CARACTERE SPECIAL.");
	}
	if(!empty($pwd) && !preg_match($regex,$pwd)){
		return array("error"=>"Le champs 'Mot de Passe' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Il doit contenir au moins une lettre MAJUSCULE, une lettre MINUSCULE, un CHIFFRE et un CARACTERE SPECIAL.");
	}
	return array("error"=>"none");
}

function validate_address($address, $regex, $required=true, $min=3, $max=100){
	if($required && empty($address)){
		return array("error"=>"Veuillez Renseigner le champs 'Adresse'.");
	}
	if(!empty($address) && (strlen($address)<$min || strlen($address)>$max)){
		return array("error"=>"Le champs 'Adresse' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les tirets '-', et les apostrophes (') ainsi que les points '.' sont autorisés.");
	}
	if(!empty($address) && !preg_match($regex,$address)){
		return array("error"=>"Le champs 'Adresse' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les tirets '-', et les apostrophes (') ainsi que les points '.' sont autorisés.");
	}
	return array("error"=>"none");
}

function validate_zip($zip, $regex, $required=true, $min=5, $max=5){
	if($required && empty($zip)){
		return array("error"=>"Veuillez Renseigner le champs 'Code postal'.");
	}
	if(!empty($zip) && (strlen($zip)<$min || strlen($zip)>$max)){
		return array("error"=>"Le champs 'Code postal' doit contenir exactement ".$min." chiffres sans espaces.");
	}
	if(!empty($zip) && !preg_match($regex,$zip)){
		return array("error"=>"Le champs 'Code postal' doit contenir exactement ".$min." chiffres sans espaces.");
	}
	return array("error"=>"none");
}

function validate_city($city, $regex, $required=true, $min=3, $max=60){
	if($required && empty($city)){
		return array("error"=>"Veuillez Renseigner le champs 'Ville'.");
	}
	if(!empty($city) && (strlen($city)<$min || strlen($city)>$max)){
		return array("error"=>"Le champs 'Ville' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les tirets '-', et les apostrophes (') ainsi que les points '.' sont autorisés.");
	}
	if(!empty($city) && !preg_match($regex,$city)){
		return array("error"=>"Le champs 'Ville' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les tirets '-', et les apostrophes (') ainsi que les points '.' sont autorisés.");
	}
	return array("error"=>"none");
}

function validate_birthdate($birthdate, $regex, $required=true){
	if($required && empty($birthdate)){
		return array("error"=>"Veuillez Renseigner le champs 'Date de naissance'.");
	}
	if(!empty($birthdate) && !preg_match($regex,$birthdate)){
		return array("error"=>"Veuillez saisir une date de naissance valide.");
	}
	return array("error"=>"none");
}

function validate_phone1($phone, $regex, $required=true){
	if($required && empty($phone)){
		return array("error"=>"Veuillez Renseigner le champs 'Téléphone'.");
	}
	if(!empty($phone) && !preg_match($regex,$phone)){
		return array("error"=>"Veuillez saisir un numéro de téléphone valide.");
	}
	return array("error"=>"none");
}

function validate_categorie($cat, $regex, $required=true, $min=3, $max=30){
	if($required && empty($cat)){
		return array("error"=>"Veuillez Renseigner le champs 'Catégorie'.");
	}
	if(!empty($cat) && (strlen($cat)<$min || strlen($cat)>$max)){
		return array("error"=>"Le champs 'Catégorie' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les points '.', les tirets '-' ainsi que les apostrophes (') sont autorisées.");
	}
	if(!empty($cat) && !preg_match($regex,$cat)){
		return array("error"=>"Le champs 'Catégorie' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les points '.', les tirets '-' ainsi que les apostrophes (') sont autorisées.");
	}
	return array("error"=>"none");
}

function validate_account($acc, $regex, $required=true, $min=3, $max=30){
	if($required && empty($acc)){
		return array("error"=>"Veuillez Renseigner le champs 'Catégorie'.");
	}
	if(!empty($acc) && (strlen($acc)<$min || strlen($acc)>$max)){
		return array("error"=>"Le champs 'Compte' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les points '.', les tirets '-' ainsi que les apostrophes (') sont autorisées.");
	}
	if(!empty($acc) && !preg_match($regex,$acc)){
		return array("error"=>"Le champs 'Compte' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Les espaces, les points '.', les tirets '-' ainsi que les apostrophes (') sont autorisées.");
	}
	return array("error"=>"none");
}

function validate_montant($amt,$regex,$required=true,$min=1,$max=8){
	if($required && empty($amt)){
		return array("error"=>"Veuillez Renseigner le champs 'Montant'.");
	}
	if(!empty($amt) && (strlen($amt)<$min || strlen($amt)>$max)){
		return array("error"=>"Le champs 'Montant' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Des chiffres uniquement ainsi que le point '.' sont autorisés");
	}
	if(!empty($amt) && !preg_match($regex,$amt)){
		return array("error"=>"Le champs 'Montant' doit contenir au minimum ".$min." caractères et au maximum ".$max." caractères. Des chiffres uniquement ainsi que le point '.' sont autorisés");
	}
	return array("error"=>"none");
}

function Url_racine_projet($racine_site){
	return sprintf(
    "%s://%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'])
    .$racine_site;
}

function curURL(){
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}

function makeForgottenEmailMsg($id,$email,$hcode,$nom,$prenom,$baseUrl){
	if(isset($GLOBALS["loggerref"])){$logger=$GLOBALS["loggerref"];$log=true;}else{$log=false;}
	if($log){$logger->info("** Dans function makeForgottenEmailMsg()","");}
	$msg="<html><head></head><body>
	        <p>
	            Bonjour, <strong>".$prenom." ".$nom."</strong>, <br /><br />

	            Afin de réinitialiser votre mot de passe, veuillez cliquer sur le lien ci dessous<br /><br />
	            <a href=\"".$baseUrl."reactivation.php?i=".$id."&hash=".$hcode."\">Réinitialiser le mot de passe</a>
				<br /><br />
	            Vous pourrez ensuite vous connecter avec vos identifiants.<br />
				<b>ATTENTION!!</b> Ce lien n'est valide que pour <b>48h</b>.<br>
				Au dela de ce délais, vous devrez refaire une demande de réinitialisation de mot de passe!!<br>
				A bientôt !
			</p></body></html>";
	if($log){$logger->info("-- Valeur de retour ",$msg);}
	return $msg;
}

function makeMsg($nom,$email,$msg,$tel){
	if(isset($GLOBALS["loggerref"])){$logger=$GLOBALS["loggerref"];$log=true;}else{$log=false;}
	if($log){$logger->info("** Dans function makeMsg()","");}
	$msg="<html><head></head><body>
	        <p>
	            Recu de, <strong>".$nom."</strong>, <br/>
	            Email: ".$email."<br>
	        	Tel: ".$tel."<br><br>
	        	Message: <br>".nl2br2($msg)."</p></body></html>";
	if($log){$logger->info("-- Valeur de retour ",$msg);}
	return $msg;
}


function mailMeViaSMPT($mail,$subject,$msg,$conf){ // $mail = PHPMailer Object
	if(isset($GLOBALS["loggerref"])){$logger=$GLOBALS["loggerref"];$log=true;}else{$log=false;}
	if($log){$logger->info("** Dans function mailMeViaSMPT()","");}
	$mail->isSMTP();
	$mail->SMTPDebug  = 0;
	$mail->CharSet     = 'UTF-8';
	$mail->Host = $conf["host"];
	if($conf["auth"]=="true"){
		if($log){$logger->info("** SMTP Auth Required...","");}
		$mail->SMTPAuth = true;                              
		$mail->Username = $conf["user"];                
		$mail->Password = $conf["userpwd"];
	}
	if($conf["secure"]!=""){
		if($log){$logger->info("** SMTP Secured Cnx...","");}
		$mail->SMTPSecure = $conf["secure"];
	}
	$mail->Port = $conf["port"];
	if($log){$logger->info("SMTP port :",$conf["port"]);}
	
	try {
		$mail->AddAddress($conf["addresseTO"], $conf["nameTO"]);
		$mail->SetFrom($conf["user"], $conf["noreplyMaster"]);
		$mail->AddReplyTo($conf["noreply"], 'no-reply');
		$mail->Subject = $subject;
		$mail->MsgHTML($msg);      // attachment
		$mail->Send();	
	}
	catch (phpmailerException $e) {
		if($log){$logger->info("** Probleme phpmailer ...",$e->getMessage());}
		return false;
	}
	catch (Exception $e) {
		if($log){$logger->info("** Probleme non phpmailer ...",$e->getMessage());}
		return false;
	}
	if($log){$logger->info("** Success ...","");}
	return true;
}






?>
<?php
/* inscription.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Inscription";

$log->info("Retour dans inscription.php", "");

if(userISConnected()){
    header("location:/main.php");
}


if($POST_Data_Avail){
    $log->info("Verification des données POST","");
    $log->info("$ POST",$_POST);
    $log->info("$ FILE",$_FILES);
    
    // traitement de la photo de profil
    $photo_dir=RACINE_SERVEUR.RACINE_SITE."assets/img/";
    $photo_bdd='default.jpg';
    
    if(isset($_POST["photo_actuelle"])){
		$photo_bdd=$_POST["photo_actuelle"];
	}
    
    if(!empty($_FILES["photo"]["name"])){
		$photo_bdd=time().'_'.rand(1,9999).$_FILES['photo']['name'];
		$authorizedImages=array("image/jpg","image/jpeg","image/png","image/gif");
		
		$log->info("photo bdd",$photo_bdd);
		$log->info("photo",$_FILES['photo']);
		
		if($_FILES['photo']['size']>2000000){
			$error['photo']="<div class='alert alert-danger'>Le fichier doit être inférieur à 2Mo.</div>";
		}
		elseif(!in_array($_FILES["photo"]["type"],$authorizedImages)){
			$error['photo']="<div class='alert alert-danger'>Le fichier doit être au format jpg, jpeg, png ou gif</div>";
		}
		elseif(!copy($_FILES["photo"]["tmp_name"], $photo_dir.$photo_bdd)){
			$error['photo']="<div class='alert alert-danger'>Erreur lors de l'enregistrement de la photo de profil</div>";
		}
		elseif(!file_exists($photo_dir.$photo_bdd)){
			$error['photo']="<div class='alert alert-danger'>Erreur lors de la creation de la photo de profil</div>";
		}
	}
	
	if(empty($error) && $photo_bdd!="default.jpg"){
		$thumb="thumb-150x-".$photo_bdd;
		$user_photo="300x-".$photo_bdd;
		if(!file_exists($thumb)){
			make_thumb($photo_dir.$photo_bdd,$photo_dir.$thumb,150);
		}
		$log->info(" $ photo_bdd",$photo_dir.$photo_bdd);
	    $log->info(" $ user_photo",$user_photo);
	    $log->info(" $ thumb",$thumb);
	}
	

    // verification de pseudo
	$log->info("Verification de pseudo","");
	$verif_pseudo=validate_pseudo($_POST["pseudo"],$regex["pseudo"],REQUIRED,3,30);
	
	if($verif_pseudo["error"]!=="none"){
		$error["pseudo"]="<span class='alert alert-danger d-block alert-dismissible'>".$verif_pseudo["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
			$log->warning("error[pseudo]",$error["pseudo"]);
	}
	else{
		try{
			$log->info("Verification de l'unicité de pseudo","");
			$resultat=$pdo->prepare("SELECT * FROM qr_user WHERE pseudo=:pseudo");
			$resultat->bindParam(':pseudo', $_POST["pseudo"]);
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
		
		if($resultat->rowCount()!=0){ // le pseudo existe deja en base de donnée
			$error["pseudo"]="<div class='alert alert-danger d-block alert-dismissible'>Pseudo non disponible<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
			$log->warning("error[pseudo]",$error["pseudo"]);
		}
		else{
			$log->info("Succes","");
		}
	}

    // verification du mot de passe
	$log->info("Verification du mot de passe","");
	$verif_mdp=validate_password($_POST['mdp'],$regex["mdp"],REQUIRED,8,15);
	if($verif_mdp["error"]!=="none"){
		$error["mdp"]= "<span class='alert alert-danger d-block alert-dismissible'>".$verif_mdp["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		$log->warning("error[mdp]",$error["mdp"]);
	}
	
    
    // verification de l'email
    $log->info("Verification de l'email","");
    $verif_email=validate_email($_POST['email'],$regex["email"],REQUIRED);
	if($verif_email["error"]!=="none"){
		$error["email"]= "<span class='alert alert-danger d-block alert-dismissible'>".$verif_email["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		$log->warning("error[email]",$error["email"]);
	}
	else{
		$log->info("Verification de l'unicité de email","");
        try{
        	$req="SELECT id FROM qr_user WHERE email=:email";
	        $resultat=$pdo->prepare($req);
	        $resultat->bindParam(":email",$_POST['email'],PDO::PARAM_STR);
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
		
        if($resultat->rowCount()!==0){
        	$error["email"]= "<span class='alert alert-danger d-block alert-dismissible'>Opération impossible!Cet Email existe déjà.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
            $log->warning("error[email]",$error["email"]);
        }
        else{
			$log->info("Succes","");
		}
    }
    
    
    // verification du nom
	$log->info("Verification du nom","");
	$verif_nom=validate_lastname($_POST['nom'],$regex["nom"],REQUIRED,3,30);
	if($verif_nom["error"]!=="none"){
		$error["nom"]= "<span class='alert alert-danger d-block alert-dismissible'>".$verif_nom["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		$log->warning("error[nom]",$error["nom"]);
	}

	
	// verification du prenom
	$log->info("Verification du prénom","");
	$verif_prenom=validate_firstname($_POST['prenom'],$regex["prenom"],REQUIRED,3,30);
	if($verif_prenom["error"]!=="none"){
		$error["prenom"]= "<span class='alert alert-danger d-block alert-dismissible'>".$verif_prenom["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		$log->warning("error[prenom]",$error["prenom"]);
	}
	
	// verification de la date de naissance
	$log->info("Verification de la date de naissance","");
	$verif_birthdate=validate_birthdate($_POST['date_de_naissance'],$regex["date_de_naissance"],REQUIRED);
	if($verif_birthdate["error"]!=="none"){
		$error["date_de_naissance"]= "<span class='alert alert-danger d-block alert-dismissible'>".$verif_birthdate["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		$log->warning("error[date_de_naissance]",$error["date_de_naissance"]);
	}
	
    
    if(empty($error)){
		// le formulaire est rempli correctement
		$log->info("Pas d'erreurs dans le formulaire","");
		
		
		
		$log->info("Insertion en BDD","");
		try{
			$req="INSERT INTO qr_user(pseudo, password,prenom,nom,email,date_de_naissance,photo,status,role,hcode,validite) ";
			$req.="VALUES (:pseudo,:mdp,:prenom,:nom,:email,:dob,:photo,'1','0',NULL,NULL)";
			$resultat=$pdo->prepare($req);
			extract($_POST);
			$resultat->bindParam(":pseudo",$pseudo,PDO::PARAM_STR);
	        $mdp=$mdp.$pseudo;
			$mdp=Bcrypt($mdp);
			$resultat->bindParam(":mdp",$mdp,PDO::PARAM_STR);
			$resultat->bindParam(":prenom",$prenom,PDO::PARAM_STR);
			$resultat->bindParam(":nom",$nom,PDO::PARAM_STR);
			$resultat->bindParam(":email",$email,PDO::PARAM_STR);
			$resultat->bindParam(":photo",$photo_bdd,PDO::PARAM_STR);
			$resultat->bindParam(":dob",$date_de_naissance,PDO::PARAM_STR);
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
		$log->info("succes!!","");
		
		$iduser=$pdo->lastInsertId();
				
		if($photo_bdd!="default.jpg"){
			$final_user_photo="user-".$iduser."-".$user_photo;
			$final_thumb="user-".$iduser."-".$thumb;
			
			
			$log->info("$ final_user_photo",$final_user_photo);
			$log->info("$ final_thumb",$final_thumb);
			
			$log->info("Renommage de la photo de profile","");
			
			if(!copy($photo_dir.$photo_bdd,$photo_dir.$final_user_photo)){
				$log->error("erreur de renommage final user photo - suppression de l'utilisateur en BDD","");
				
				try{
					$resultat=$pdo->prepare("DELETE FROM qr_user WHERE id=:id");
					$resultat->bindParam(":pseudo",$iduser,PDO::PARAM_INT);
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
				$log->error("- Inscription non effectuée","");
				unlink($photo_dir.$photo_bdd);
				unlink($photo_dir.$thumb);
				$error["notice"]="<div class='alert alert-danger text-center d-block'>Une erreur est survenue. Veuillez essayer de valider de nouveau.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
			}
			else{
				unlink($photo_dir.$photo_bdd);
				$log->info("Renommage du thumbnail de profile","");
				if(!copy($photo_dir.$thumb,$photo_dir.$final_thumb)){
					$log->error("erreur de renommage du thumb - suppression de l'utilisateur en BDD","");
					
					try{
						$resultat=$pdo->prepare("DELETE FROM qr_user WHERE id=:id");
						$resultat->bindParam(":pseudo",$iduser,PDO::PARAM_INT);
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
					
					$log->error("- Inscription non effectuée","");
					unlink($photo_dir.$photo_bdd);
					unlink($photo_dir.$thumb);
					$error["notice"]="<div class='alert alert-danger text-center d-block'>Une erreur est survenue. Veuillez essayer de valider de nouveau.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
				}
				else{
					unlink($photo_dir.$thumb);
				}
			}
		}
		
		$log->info("succes","");
		if(!isset($error["notice"])){
			$log->info("login automatique et redirection vers profil.php","");
			$_SESSION["user"]=$_POST;
			$_SESSION["thread"]["title"]=$_POST["mdp"];
			unset($_SESSION["user"]["mdp"]);
			unset($_SESSION["user"]["photo_actuelle"]);
			unset($_SESSION["user"]["hcode"]);
			unset($_SESSION["user"]["validite"]);
			$_SESSION["user"]["id"]=$iduser;
			$_SESSION["user"]["status"]=1;
			$_SESSION["user"]["role"]=0;
			
			if($photo_bdd!="default.jpg"){
				$_SESSION["user"]["photo"]=$final_user_photo;
			}
			else{
				$_SESSION["user"]["photo"]=$photo_bdd;
			}
			
			// update de la photo en bdd
			$log->info("update de la photo en BDD","");
			
			try{
				$resultat=$pdo->prepare("UPDATE qr_user SET photo=:photo WHERE id=:id");
				$resultat->bindParam(":id",$iduser,PDO::PARAM_INT);
				$resultat->bindParam(":photo",$_SESSION["user"]["photo"],PDO::PARAM_INT);
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
			
			// verification - creation de la catégorie 'aucune'
			
			try{
				$log->info("verification - creation de la catégorie 'aucune'","");
				$resultat=$pdo->prepare("SELECT nom_categorie FROM categories WHERE nom_categorie='aucune' AND id_user=:id_user");
				$resultat->bindParam(":id_user",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
			
			if($resultat->rowCount()==0){
				$log->info("La catégorie 'aucune' n'existe pas - On la crée","");
				
				try{
					$resultat=$pdo->prepare("INSERT INTO categories (id_user, nom_categorie) VALUES(:id_user, 'aucune')");
					$resultat->bindParam(":id_user",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
			}
			else{
				$log->info("La catégorie 'aucune' existe déjà","");
			}
			$log->info("\$_SESSION[user]", $_SESSION['user']);
			
			$log->info("redirection vers le profil utilisateur","");
			
			header("location:./profil.php");
		}
	}
	else{
		$error["notice"]="<div class='alert alert-danger text-center d-block'>Des erreurs existent dans le Formulaire. Veuillez corriger et valider de nouveau.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
	}
}

extract($_POST);
$pseudo_r=(isset($_POST["pseudo"]))?$_POST["pseudo"]:"";
$mdp_r=(isset($_POST["mdp"]))?$_POST["mdp"]:""; 
$nom_r=(isset($_POST["nom"]))?$_POST["nom"]:"";
$prenom_r=(isset($_POST["prenom"]))?$_POST["prenom"]:"";
$email_r=(isset($_POST["email"]))?$_POST["email"]:"";
$date_de_naissance_r=(isset($_POST["date_de_naissance"]))?$_POST["date_de_naissance"]:"";
$photo_r=(isset($photo_bdd))?$photo_bdd:"default.jpg";


//--PAGE TRANSITIONS
/**********************/
//$jcr_page_transition=set_jcr_page_transition(4,"strict");
$jcr_page_transition=set_jcr_page_transition();

$log->info("Affichage du HTML", "");
/**********************/
closeCnx($pdo);
$log->info("destruction de l'objet PDO", $pdo);
$log->stop();
$log->kill();
//*************  AFFICHAGE du HTML
require_once("./include/chunks/header.inc.php");
?>
<section class="container">
<div class="row">
    <?=(isset($error["notice"]))?$error["notice"]:"";?>
    
    <div class="col-xs-11 col-sm-11 m-auto">
    <form action="inscription.php" method="post" autocomplete="on" id="inscr" enctype="multipart/form-data">
    	
        <div class="form-group">
            <label for="ident">Pseudo: (*)</label>
            <input type="text" class="form-control <?php if(isset($error["pseudo"])){ echo "border border-danger";}  ?>" id="ident" name="pseudo" value="<?= $pseudo_r ?>" aria-describedby="login" placeholder="Pseudo" maxlength="30" pattern="/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ\.\-\_]{3,20}$/"  title="3 à 30 caractères sans espaces ni caractères spéciaux." required>
            <small id="passwordHelpBlock" class="form-text text-muted">
                3 à 20 caractères sans espaces ni caractères spéciaux.
            </small>
            <span class="FieldError"></span>
            <?php if(isset($error["pseudo"])){ echo $error["pseudo"];}  ?>
        </div>

        <div class="form-group">
            <label for="pwd">Mot de passe: (*)</label>
            <input type="password" class="form-control" id="pwd" name="mdp" value="<?= $mdp_r ?>" aria-describedby="password" placeholder="Mot de passe" maxlength="15"  required>
            <small id="passwordHelpBlock" class="form-text text-muted">
                8 à 15 caractères. Doit comporter au moins une majuscule, une minuscule, un chiffres et un caractères spécial.
            </small>
            <span class="FieldError"></span>
            <?php if(isset($error["mdp"])){ echo $error["mdp"];}  ?>
        </div>

        <div class="form-group">
            <label for="lname">Nom: (*)</label>
            <input type="text" class="form-control" id="lname" name="nom" value="<?=$nom_r ?>" maxlength="30" aria-describedby="last name" placeholder="Nom" pattern="/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}+([\'\s-]{0,1}[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}){0,4}$/"  title="3 caractères minimum" required>
            <small id="passwordHelpBlock" class="form-text text-muted">
                3 caractères au minimum.
            </small>
            <span class="FieldError"></span>
            <?php if(isset($error["nom"])){ echo $error["nom"];}  ?>
        </div>

        <div class="form-group">
            <label for="fname">Prénom: (*)</label>
            <input type="text" class="form-control" id="fname" name="prenom" value="<?=$prenom_r ?>" maxlength="30" aria-describedby="first name" placeholder="Prénom" pattern="/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{3,30}+([\'\s-]{0,1}[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}){0,4}$/"  title="3 caractères minimum" required>
            <small id="passwordHelpBlock" class="form-text text-muted">
                3 caractères au minimum.
            </small>
            <span class="FieldError"></span>
            <?php if(isset($error["prenom"])){ echo $error["prenom"];}  ?>
        </div>

        <div class="form-group">
            <label for="mail">Email: (*)</label>
            <input type="email" class="form-control" id="mail" name="email" value="<?=$email_r ?>" aria-describedby="Email" placeholder="Email" required>
            <span class="FieldError"></span>
            <?php if(isset($error["email"])){ echo $error["email"];}  ?>
        </div>

        <div class="form-group">
            <label for="dob">Date de naissance: (*)</label>
            <input type="date" class="form-control" id="dob" name="date_de_naissance" value="<?= $date_de_naissance_r ?>" aria-describedby="date de naissance" placeholder="Date de naissance" required>
            <span class="FieldError"></span>
            <?php if(isset($error["date_de_naissance"])){ echo $error["date_de_naissance"];}  ?>
        </div>
        
        <div class="form-group">
			<label for="pic">Charger une photo</label>
			<input class="form-control" type="file" id="pic" name="photo" aria-describedby="picture" >
			<?php if (isset($photo_r)): ?>
			<small id="defpic" class="form-text text-muted">Fichier chargé: <?=$photo_r?></small>
			<?php endif; ?>
			<small id="pict" class="form-text text-muted">2Mo max...</small>
			<span class="FieldError"></span>
		    <?php if(isset($error["photo"])){ echo $error["photo"];}  ?>
		    <input type="hidden" name="photo_actuelle" value="<?=$photo_r?>">
		</div>
		
        <button type="submit" class="btn btn-primary">Valider</button>
    </form>
    </div>
</div>
</section>

<?php
require_once("./include/chunks/footer.inc.php");
?>
<?php
/* connexion.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="reactivation";

$log->info("Retour dans reactivation.php", "");

if(userISConnected()){
header("location:/main.php");
}

if($GET_Data_Avail){
	if(isset($_GET["i"]) && !empty($_GET["i"]) && is_numeric($_GET["i"]) && isset($_GET["hash"]) && !empty($_GET["hash"])){
		extract($_GET);
		$req="SELECT pseudo,hcode FROM qr_user WHERE id=:id AND NOW()<=validite";
		try{
			$resultat=$pdo->prepare($req);
			$resultat->bindParam(":id",$i,PDO::PARAM_INT);
			$resultat->execute();
		}
		catch(PDOException $e){
			$log->fatal("erreur PDO1",$e->xdebug_message);
			die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px;text-align:center;'>Nous n'avons pas pu vérifier vos données. Veuillez recharger la page.</div>");
			
		}
		catch(Exception $e){
			$log->fatal("erreur PDO2",$e->xdebug_message);
			die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px'>Nous n'avons pas pu vérifier vos données. Veuillez recharger la page.</div>");
		}
		
		if($resultat->rowCount()==0){
			$errorget="<div class='alert alert-danger text-center d-block'>Votre lien a expiré. Veuillez refaire la demande de réinitialisation de votre mot de passe, en cliquant sur le lien 'mot de passe oublié' de la <a href='./connexion.php'>page de connexion</a><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
		}
		else{
			$userdata=$resultat->fetch();
			$pseudo=$userdata["pseudo"];
			$hcode=$userdata["hcode"];
			if(!Dcrypt($hash,$hcode)){
				$errorget="<div class='alert alert-danger text-center d-block'>Le lien utilisé est devenu obsolète. Veuillez refaire la demande de réinitialisation de votre mot de passe, en cliquant sur le lien 'mot de passe oublié' de la <a href='./connexion.php'>page de connexion</a><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
			}
		}
	}
	else{
		die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px;text-align:center;'>Nous n'avons pas pu vérifier vos données. Veuillez recharger la page.</div>");
	}
}


if($POST_Data_Avail && !isset($errorget)){
	// verification du mot de passe
	$log->info("Verification du mot de passe 1","");
	$verif_mdp=validate_password($_POST['mdp1'],$regex["mdp"],REQUIRED,8,15);
	if($verif_mdp["error"]!=="none"){
		$error["mdp1"]= "<span class='alert alert-danger d-block alert-dismissible'>".$verif_mdp["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		$log->warning("error[mdp1]",$error["mdp1"]);
	}
	
	$log->info("Verification du mot de passe 2","");
	$verif_mdp=validate_password($_POST['mdp2'],$regex["mdp"],REQUIRED,8,15);
	if($verif_mdp["error"]!=="none"){
		$error["mdp2"]= "<span class='alert alert-danger d-block alert-dismissible'>".$verif_mdp["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		$log->warning("error[mdp2]",$error["mdp2"]);
	}
	
	if(empty($error)){
		if($_POST['mdp1']!==$_POST['mdp2']){
			$notice.="<div class='alert alert-danger text-center d-block'>Les mots de passe saisis ne sont pas identiques.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
		}
	}
	
	if(empty($error) && empty($notice)){
		$mdp=$_POST["mdp1"].$pseudo;
		$mdp=Bcrypt($mdp);
		try{
			$req="UPDATE qr_user SET password=:mdp, hcode=NULL, validite=NULL WHERE id=:id";
			$resultat=$pdo->prepare($req);
			$resultat->bindParam(":mdp",$mdp,PDO::PARAM_STR);
			$resultat->bindParam(":id",$i,PDO::PARAM_INT);
			$resultat->execute();
		}
		catch(PDOException $e){
			$log->fatal("erreur PDO1",$e->xdebug_message);
			die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px;text-align:center;'>Nous n'avons pas pu vérifier vos données. Veuillez recharger la page.</div>");
			
		}
		catch(Exception $e){
			$log->fatal("erreur PDO2",$e->xdebug_message);
			die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px'>Nous n'avons pas pu vérifier vos données. Veuillez recharger la page.</div>");
		}
		
		$success["success"]="<div class='alert alert-success text-center d-block'>Mot de passe correctement ré-initialisé. <a href='./connexion.php'>Connexion</a><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
		
		
	}
	else{
		if(empty($notice)){
			$notice.="<div class='alert alert-danger text-center d-block'>Des erreurs existent dans le Formulaire. Veuillez corriger et valider de nouveau.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
		}
	}
}
$mdp1_r=(isset($_POST["mdp1"]))?$_POST["mdp1"]:"";
$mdp2_r=(isset($_POST["mdp2"]))?$_POST["mdp2"]:"";

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

<?PHP if(isset($errorget)):?>
	<?=$errorget?>
<?php else: ?>
	<?PHP if(isset($success["success"])):?>
		<?=$success["success"]?>
	<?php else: ?>
		<section class="container">
			<h2>Nouveau Mot de passe</h2>
			<div class="row">
				
				<div class="col-sm-5 m-auto border border-light rounded shadow-lg">
					<?=$notice?>
					<form action="reactivation.php?i=<?=$_GET['i']?>&hash=<?=$_GET['hash']?>" method="post">
						<div class="form-group">
							<label class="d-block" for="ancienmdp">Nouveau mot de passe:</label>
							<input type="password" class="form-control w-75" name="mdp1" placeholder="..." value="<?=$mdp1_r?>" required>
							<small id="passwordHelpBlock" class="form-text text-muted">
				                8 à 15 caractères. Doit comporter au moins une majuscule, une minuscule, un chiffres et un caractères spécial.
				            </small>
				            <span class="FieldError"></span>
				            <?php if(isset($error["mdp1"])){ echo $error["mdp1"];}  ?>
						</div>
						
						<div class="form-group">
							<label class="d-block" for="ancienmdp">Confirmez le mot de passe:</label>
							<input type="password" class="form-control w-75" name="mdp2" placeholder="..." value="<?=$mdp2_r?>" required>
				            <span class="FieldError"></span>
				            <?php if(isset($error["mdp2"])){ echo $error["mdp2"];}  ?>
						</div>
						
						<div class="form-group">
							<input type="submit" class="btn btn-outline-primary d-block w-50" value="Envoyer" />
						</div>
					</form>
				</div>
			</div>
			
		</section>
	<?php endif;?>
<?php endif;?>
<?php
require_once("./include/chunks/footer.inc.php");
?>
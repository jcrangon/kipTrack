<?php
/* category.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Ajout catégorie";

$log->info("Retour dans accueil.php", "");

if(!userISConnected()){
	header("location:./accueil.php");
}

if($GET_Data_Avail){
	$log->info("Verification des données GET","");
	if(isset($_GET["id"]) && is_numeric($_GET["id"])){
		try{
			$resultat=$pdo->prepare("SELECT * FROM categories WHERE id=:id");
			$resultat->bindParam(":id", $_GET["id"]);
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
		
		if($resultat->rowCount()!==0){
			$categorie=$resultat->fetch();
			$log->info("categorie à modifier", $categorie);
			$modify=true;
		}
	}
}


if($POST_Data_Avail){
	$log->info("Verification des données POST","");
	
    // verification du nom de la categorie
	$log->info("Verification du nom de categorie","");
	$verif_cat=validate_categorie($_POST["nom_categorie"],$regex["categorie"],REQUIRED,3,20);
	if($verif_cat["error"]!=="none"){
		$error["nom_categorie"]="<span class='alert alert-danger d-block alert-dismissible'>".$verif_cat["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		$log->warning("error[nom_categorie]",$error["nom_categorie"]);
	}
	
	
	
	if(empty($error)){
		// le formulaire est rempli correctement
		if(!$modify){

			try{
				$resultat=$pdo->prepare("SELECT * FROM categories WHERE nom_categorie=:nom_categorie AND id_user=:id_membre");
				$resultat->bindParam(":nom_categorie",$_POST["nom_categorie"]);
				$resultat->bindParam(":id_membre",$_SESSION["user"]["id"]);
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
			
			if($resultat->rowCount()!=0){ // le nom_categorie existe deja en base de donnée
				$error["nom_categorie"]="<div class='alert alert-danger d-block alert-dismissible'>Ce nom existe déjà. Veuillez en choisir un autre.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
				$notice.="<div class='alert alert-danger text-center d-block'>Des erreurs existent dans le formulaire<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
				$log->warning("error[nom_categorie]",$error["nom_categorie"]);
			}
			else{ // on peut enregistrer l'inscription
				$log->info("Insertion nouvelle categorie en BDD","");
				try{
					$req="INSERT INTO categories(id_user, nom_categorie) ";
					$req.="VALUES (:id_membre,:nom_categorie)";
					$resultat=$pdo->prepare($req);
					extract($_POST);
					$nom_categorie=mb_strtolower($nom_categorie);
					$resultat->bindParam(":id_membre",$_SESSION["user"]["id"],PDO::PARAM_INT);
					$resultat->bindParam(":nom_categorie",$nom_categorie,PDO::PARAM_STR);
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
				header("location:./categories.php");
			}
		}
		else{
			try{
				$resultat=$pdo->prepare("SELECT * FROM categories WHERE nom_categorie=:nom_categorie AND id_user=:id_membre AND id<>:id");
				$resultat->bindParam(":nom_categorie",$_POST["nom_categorie"]);
				$resultat->bindParam(":id_membre",$_SESSION["user"]["id"]);
				$resultat->bindParam(":id",$_GET["id"]);
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
			
			
			if($resultat->rowCount()!=0){ // le nom_categorie existe deja en base de donnée
				$error["nom_categorie"]="<div class='alert alert-danger d-block alert-dismissible'>Ce nom existe déjà. Veuillez en choisir un autre.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
				$notice.="<div class='alert alert-danger text-center d-block'>Des erreurs existent dans le formulaire<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
				$log->warning("error[nom_categorie]",$error["nom_categorie"]);
			}
			else{
				$log->info("Modification de la categorie en BDD","");
				try{
					$req="UPDATE categories SET nom_categorie=:nom_categorie WHERE id=:id";
					$resultat=$pdo->prepare($req);
					extract($categorie);
					$nom_cat=mb_strtolower($_POST["nom_categorie"]);
					$resultat->bindParam(":nom_categorie", $nom_cat,PDO::PARAM_STR);
					$resultat->bindParam(":id", $id,PDO::PARAM_INT);
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
				header("location:./categories.php");
			}
		}
	}
	else{
		$notice.="<div class='alert alert-danger text-center d-block'>Des erreurs existent dans le Formulaire. Veuillez corriger et valider de nouveau.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
	}
}
extract($_POST);

if(!$modify){
	$nom_categorie_r=(isset($_POST["nom_categorie"]))?$_POST["nom_categorie"]:"";
}
else{
	if(isset($_POST["nom_categorie"])){
		$nom_categorie_r=ucfirst($_POST["nom_categorie"]);
	}
	else{
		$nom_categorie_r=ucfirst($categorie["nom_categorie"]);
	}
	
}

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
	<?php if($modify):?>
	<h2>Modifier une <span class="categorie">catégorie</span><span class="categorie plus"><a href="./categories.php" title="Gérer les catégories"><i class="fas fa-eye"></i></a></span></h2>
	<?php else: ?>
	<h2>Ajouter une <span class="categorie">catégorie</span><span class="categorie plus"><a href="./categories.php" title="Gérer les catégories"><i class="fas fa-eye"></i></a></span></h2>
	<?php endif; ?>
	
	<?=$notice;?>
	
	<section class="form1Container">
		<p>Veuillez utiliser un nom sans équivoque. Par exemple 'Vêtements' ou 'Courses'</p>
		<form class="formStyle1" id="newCat" action="ajout-categorie.php<?=($modify==true)?"?id=".$categorie["id"]:""?>" method="post" autocomplete="on">
			<fieldset>
				
				<div class="inputGroup">
					<input class="textEffect1" id="catName" name="nom_categorie" type="text" placeholder="Nom de la catégorie" maxlength="20" title="3 caractères minimum" required value="<?php if(isset($nom_categorie_r)){echo $nom_categorie_r; }?>">
					<label for="catName">Nom de la catégorie</label>
					<span class="requiredField">*</span>
					<span class="fieldError"><?=(isset($error["nom_categorie"]))?$error["nom_categorie"]:"" ?></span>
				</div>
				
				<div class="inputGroup">
		        	<input class="btn btn-warning" type="submit" value="Valider"><span><a class="btn btn-outline-warning" href="./categories.php">Annuler</a></span>
		        </div>
		        
			</fieldset>
		</form>
	</section>

</section>
<?php
require_once("./include/chunks/footer.inc.php");
?>
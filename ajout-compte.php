<?php
/* ajout-compte.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Ajout compte";

$log->info("Retour dans ajout-compte.php", "");

if(!userISConnected()){
header("location:./accueil.php");
}

if($GET_Data_Avail){
	$log->info("Verification des données GET","");
	if(isset($_GET["id"]) && is_numeric($_GET["id"])){

		try{
			$resultat=$pdo->prepare("SELECT * FROM account WHERE id=:id");
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
			$account=$resultat->fetch();
			$log->info("compte à modifier", $account);
			$modify=true;
		}
	}
}


if($POST_Data_Avail){
	$log->info("Verification des données POST","");
	
    // verification de nom_account
	$log->info("Verification du nom de compte","");
	$verif_compte=validate_account($_POST["nom_account"],$regex["compte"],REQUIRED,3,20);
	if($verif_compte["error"]!=="none"){
		$error["nom_account"]="<span class='alert alert-danger d-block alert-dismissible'>".$verif_compte["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		$log->warning("error[nom_account]",$error["nom_account"]);
	}
	
	if(empty($error)){
		// le formulaire est rempli correctement

		if(!$modify){ // creation initiale du compte
			
			try{
				$resultat=$pdo->prepare("SELECT * FROM account WHERE nom_account=:nom_account AND id_membre=:id_membre");
				$resultat->bindParam(":nom_account",$_POST["nom_account"]);
				$resultat->bindParam(":id_membre",$_SESSION["user"]["id"]);
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
		
		
			if($resultat->rowCount()!=0){ // le nom_account existe deja en base de donnée
				$error["nom_account"]="<div class='alert alert-danger d-block alert-dismissible'>Ce nom existe déjà. Veuillez en choisir un autre.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
				$notice.="<div class='alert alert-danger text-center d-block'>Des erreurs existent dans le formulaire<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
				$log->warning("error[nom_account]",$error["nom_account"]);
			}
			else{ // on peut enregistrer l'inscription
				try{
					$log->info("Insertion nouveau compte en BDD","");
					$req="INSERT INTO account(id_membre, nom_account,solde) ";
					$req.="VALUES (:id_membre,:nom_account,0)";
					$resultat=$pdo->prepare($req);
					extract($_POST);
					$nom_account=mb_strtolower($nom_account);
					$resultat->bindParam(":id_membre",$_SESSION["user"]["id"],PDO::PARAM_INT);
					$resultat->bindParam(":nom_account",$nom_account,PDO::PARAM_STR);
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
				$log->info("succes!!","");
				
				// verification et creation de la catégorie 'versement' dans la table categories
				
				try{
					$resultat=$pdo->prepare("SELECT id FROM categories WHERE nom_categorie='versement' AND id_user=:id_user");
					$resultat->bindParam(":id_user",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
					$log->info("Insertion categorie 'versement' BDD","");
					try{
						$resultat=$pdo->prepare("INSERT INTO categories (id_user, nom_categorie) VALUES(:id_user, 'versement')");
						$resultat->bindParam(":id_user",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
					$log->info("succes!!","");
					
				}
				header("location:./compte.php");
			}
		}
		else{ // modification d'un compte existant
			try{
				$resultat=$pdo->prepare("SELECT * FROM account WHERE nom_account=:nom_account AND id_membre=:id_membre AND id<>:id");
				$resultat->bindParam(":nom_account",$_POST["nom_account"]);
				$resultat->bindParam(":id_membre",$_SESSION["user"]["id"]);
				$resultat->bindParam(":id",$_GET["id"]);
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
			
			if($resultat->rowCount()!=0){ // le nom_account existe deja en base de donnée
				$error["nom_account"]="<div class='alert alert-danger d-block alert-dismissible'>Ce nom existe déjà. Veuillez en choisir un autre.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
				$notice.="<div class='alert alert-danger text-center d-block'>Des erreurs existent dans le formulaire<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
				$log->warning("error[nom_account]",$error["nom_account"]);
			}
			else{
				
				$log->info("Modification du compte en BDD","");
				try{
					$req="UPDATE account SET nom_account=:nom_account WHERE id=:id";
					$resultat=$pdo->prepare($req);
					extract($account);
					$nom_acc=mb_strtolower($_POST["nom_account"]);
					$resultat->bindParam(":nom_account", $nom_acc,PDO::PARAM_STR);
					$resultat->bindParam(":id", $id,PDO::PARAM_INT);
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
				
				$log->info("succes!!","");
				header("location:./compte.php");
			}
		}
	}
	else{
		$notice.="<div class='alert alert-danger text-center d-block'>Des erreurs existent dans le Formulaire. Veuillez corriger et valider de nouveau.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
	}
}

extract($_POST);
if(!$modify){
	$nom_account_r=(isset($_POST["nom_account"]))?$_POST["nom_account"]:"";
}
else{
	if(isset($_POST["nom_account"])){
		$nom_account_r=ucfirst($_POST["nom_account"]);
	}
	else{
		$nom_account_r=ucfirst($account["nom_account"]);
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
	<h2>Modifier un <span class="compte">compte</span><span class="compte plus"><a href="./compte.php" title="Gérer les comptes"><i class="fas fa-eye"></i></a></span></h2>
	<?php else: ?>
	<h2>Ajouter un <span class="compte">compte</span><span class="compte plus"><a href="./compte.php" title="Gérer les comptes"><i class="fas fa-eye"></i></a></span></h2>
	<?php endif; ?>
	
	<?=$notice;?>
	
	<section class="form1Container">
		<p>Veuillez utiliser un nom évocateur. Par exemple 'Compte Espèce 1' ou 'Compte BNP 1'</p>
		<form class="formStyle1" id="newContact" action="ajout-compte.php<?=($modify==true)?"?id=".$account["id"]:""?>" method="post" autocomplete="on">
			<fieldset>
				
				<div class="inputGroup">
					<input class="textEffect1" id="accName" name="nom_account" type="text" placeholder="Nom du compte" maxlength="20" title="3 caractères minimum" required value="<?php if(isset($nom_account_r)){echo $nom_account_r; }?>">
					<label for="accName">Nom du compte</label>
					<span class="requiredField">*</span>
					<span class="fieldError"><?=(isset($error["nom_account"]))?$error["nom_account"]:"" ?></span>
				</div>
				
				<div class="inputGroup">
		        	<input class="btn btn-success" type="submit" value="Valider"><span><a class="btn btn-outline-success" href="./compte.php">Annuler</a></span>
		        </div>
		        
			</fieldset>
		</form>
	</section>

</section>
		
<?php
require_once("./include/chunks/footer.inc.php");
?>
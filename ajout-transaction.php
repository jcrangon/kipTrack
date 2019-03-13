<?php
/* transactions.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Ajout transaction";

$log->info("Retour dans ajout-transaction.php", "");

if(!userISConnected()){
header("location:./accueil.php");
}
$no_account_found=false;


// liste des categories appartenant a l'utilisateur
// necessaire pour remplir le select du formulaire

try{
	$resultat=$pdo->prepare("SELECT id, nom_categorie FROM categories WHERE id_user = :id_user");
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

if($resultat->rowCount()!=0){
	$liste_cat=$resultat->fetchAll(PDO::FETCH_ASSOC);
	$log->info("liste des categories user",$liste_cat);
}
else{
	$log->warning("Aucune Catégories trouvées",$resultat->rowCount());
	die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px'>Votre compte a été corrompu. Veuillez <a href='./contact.php'>contacter le webmaster.</a></div>");
}

// liste des nom et id des comptes de l'utilisateur
// necessaire pour remplir le select du formulaire

try{
	$resultat=$pdo->prepare("SELECT id, nom_account FROM account WHERE id_membre = :id_membre");
	$resultat->bindParam(":id_membre",$_SESSION["user"]["id"],PDO::PARAM_INT);
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


if($resultat->rowCount()!=0){
	$liste_compte=$resultat->fetchAll(PDO::FETCH_ASSOC);
	$log->info("liste des comptes user",$liste_compte);
}
else{
	$notice.="<span class='alert alert-danger d-block alert-dismissible'>Nous n'avons pas trouvé de comptes vous appartenant. Veuillez <a href='./ajout-compte.php'>creer un compte</a><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
	$log->warning("Comptes trouvées",$resultat->rowCount());
	$no_account_found=true;
}


if($GET_Data_Avail){
	$log->info("$ GET",$_GET);
	if(isset($_GET["id"]) && !empty($_GET["id"])){
		try{
			$resultat=$pdo->prepare("SELECT * FROM transactions WHERE id=:id");
			$resultat->bindParam(":id",$_GET["id"],PDO::PARAM_STR);
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
		
		if($resultat->rowCount()!=0){
			$transacmodif=$resultat->fetch();
			$log->info("transaction à modifier", $transacmodif);
			$modify=true;
		}
	}
}


//recuperation des données formulaire
if($POST_Data_Avail){
	$log->info("Verification des données POST","");
	
    // verification de nom_account
	$log->info("Verification du montant","");
	$verif_montant=validate_montant($_POST["montant"],$regex["montant"],REQUIRED,1,8);
	if($verif_montant["error"]!=="none"){
		$error["montant"]="<span class='alert alert-danger d-block alert-dismissible'>".$verif_montant["error"]."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		$log->warning("error[montant]",$error["montant"]);
	}
	
	
	// verification du champ 'date'
	if(empty($_POST["date"])){
		$_POST["date"]=date("Y-m-j");
	}
	
	
	// Pas d'erreur de saisie :
	if(empty($error)){
		
		if(!$modify){ // requete d'insertion
			
			try{
				$resultat=$pdo->prepare("INSERT INTO transactions (id_user, date,  montant, categorie, compte, moypay, memo) VALUES(:id_user, :date, :montant, :categorie, :compte, :moypay, :memo)");
				$resultat->bindParam(":id_user",$_SESSION["user"]["id"],PDO::PARAM_INT);
				$resultat->bindParam(":date",$_POST["date"],PDO::PARAM_STR);
				$resultat->bindParam(":montant",$_POST["montant"],PDO::PARAM_STR);
				$resultat->bindParam(":categorie",$_POST["categorie"],PDO::PARAM_INT);
				$resultat->bindParam(":compte",$_POST["compte"],PDO::PARAM_INT);
				$resultat->bindParam(":moypay",$_POST["moypay"],PDO::PARAM_INT);
				$resultat->bindParam(":memo",$_POST["memo"],PDO::PARAM_STR);
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
			
			// update du solde du compte concerné
			$log->info("update du solde du compte concerné","");
			
			try{
				$resultat=$pdo->prepare("UPDATE account SET solde=solde-:montant WHERE id=:id_account");
				$resultat->bindParam(":montant",$_POST["montant"],PDO::PARAM_STR);
				$resultat->bindParam(":id_account",$_POST["compte"],PDO::PARAM_INT);
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
			
			$success["success"]="<span class='alert alert-success d-block alert-dismissible'>Transaction validée avec succes! <a class='btn btn-outline-success' href='./ajout-transaction.php'>Nouvelle Transation</a> <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
		}
		else{
			// requete UPDATE
			try{
				$resultat=$pdo->prepare("UPDATE transactions SET date=:date, montant=:montant, categorie=:categorie, compte=:compte, moypay=:moypay, memo=:memo WHERE id=:id");
				$resultat->bindParam(":date", $_POST["date"],PDO::PARAM_STR);
				$resultat->bindParam(":montant",$_POST["montant"],PDO::PARAM_STR);
				$resultat->bindParam(":categorie",$_POST["categorie"],PDO::PARAM_INT);
				$resultat->bindParam(":compte",$_POST["compte"],PDO::PARAM_INT);
				$resultat->bindParam(":moypay",$_POST["moypay"],PDO::PARAM_INT);
				$resultat->bindParam(":memo",$_POST["memo"],PDO::PARAM_STR);
				$resultat->bindParam(":id",$transacmodif["id"],PDO::PARAM_STR);
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
			
			if(!$resultat){
				$notice="<span class='alert alert-danger d-block alert-dismissible'>Une erreur est survenue! Veuillez de nouveau soumettre le formulaire<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
			}
			else{// update du solde du compte concerné
				
				try{
					$resultat=$pdo->prepare("UPDATE account SET solde=solde+:oldmontant WHERE id=:id_oldaccount");
					$resultat->bindParam(":oldmontant",$transacmodif["montant"],PDO::PARAM_STR);
					$resultat->bindParam(":id_oldaccount",$transacmodif["compte"],PDO::PARAM_INT);
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
				
				
				try{
					$resultat=$pdo->prepare("UPDATE account SET solde=solde-:montant WHERE id=:id_account");
					$resultat->bindParam(":montant",$_POST["montant"],PDO::PARAM_STR);
					$resultat->bindParam(":id_account",$_POST["compte"],PDO::PARAM_INT);
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
				
				$success["success"]="<span class='alert alert-success d-block alert-dismissible'>Transaction validée avec succes! <a class='btn btn-outline-success' href='./ajout-transaction.php'>Nouvelle Transation</a> <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
			}
		}
		
	}
	else{
		$notice="<span class='alert alert-danger d-block alert-dismissible'>Des erreurs existent dans le formulaire.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></span>";
	}
}
if(!$modify){
	extract($_POST);
	$date_r=(isset($date))?$date:date("Y-m-d");
	$montant_r=(isset($montant))?floatval($montant):"";
	$categorie_r=(isset($categorie))?ucfirst($categorie):"";
	$compte_r=(isset($compte))?ucfirst($compte):"";
	$moypay_r=(isset($moypay))?$moypay:"";
	$memo_r=(isset($memo))?$memo:"";
}
else{
	$date_r=(isset($_POST["date"]))?$_POST["date"]:$transacmodif["date"];
	$montant_r=(isset($_POST["montant"]))?floatval($_POST["montant"]):floatval($transacmodif["montant"]);
	$categorie_r=(isset($_POST["categorie"]))?ucfirst($_POST["categorie"]):ucfirst($transacmodif["categorie"]);
	$compte_r=(isset($_POST["compte"]))?ucfirst($_POST["compte"]):ucfirst($transacmodif["compte"]);
	$moypay_r=(isset($_POST["moypay"]))?$_POST["moypay"]:$transacmodif["moypay"];
	$memo_r=(isset($_POST["memo"]))?$_POST["memo"]:$transacmodif["memo"];
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
	<h2><?=($modify)?"Modifier":"Ajouter";?> une <span class="transactions">transaction</span><span class="transactions plus"><a href="./transactions.php" title="Registre des transaction"><i class="fas fa-eye"></i></a></span></h2>

<div class="row">
	<div class="col-xl-6 offset-xl-3 col-lg-6 offset-lg-3 col-sm-10 offset-sm-1 bg-light">
		
	    <?=($notice!=="")?$notice:""?>
	    <?=(isset($success["success"]))?$success["success"]:"";?>
	    
	    <form id="transac" action="<?=($modify)?"./ajout-transaction.php?id=".$transacmodif["id"]:"./ajout-transaction.php";?>" method="post">
	        
	        <div class="form-group">
			   <label for="datetransac">Date</label>
			   <input type="date" class="form-control" id="datetransac" name='date' value="<?=$date_r?>" required>
			</div>
			
	        <div class="form-group">
	           <input type="text" class="form-control" id="amount" name="montant" value="<?=($montant_r!=="")?number_format($montant_r,2,".",""):""?>" maxlength="8" placeholder="€" pattern="[0-9\.]{1,}" required>
	           <?=(isset($error["montant"]))?$error["montant"]:"";?>
	        </div>
	        
	        <div class="form-group">
	           <label for="catsel">Catégorie :</label>
	           <select class="custom-select" id="catsel" name="categorie">
	           		<?php foreach($liste_cat as $cat): ?>
	           			<?php if($cat["nom_categorie"]!="versement"): ?>
		           			<?php if($categorie_r=="" && $cat["nom_categorie"]=="aucune"):?>
		           				<option value="<?=$cat["id"];?>" selected><?=ucfirst($cat["nom_categorie"]);?></option>
		           			<?php elseif($cat["id"]==$categorie_r):?>
		           				<option value="<?=$cat["id"];?>" selected><?=ucfirst($cat["nom_categorie"]);?></option>
		           			<?php else:?>
		           				<option value="<?=$cat["id"];?>"><?=ucfirst($cat["nom_categorie"]);?></option>
		           			<?php endif;?>
	           			<?php endif; ?>
	           		<?php endforeach;?>
	           </select>
	       </div>
	        
	        <div class="form-group">
	           <label for="moyenpay">Moyen de paiement :</label>
	           <select class="custom-select" id="moyenpay" name="moypay" required>
	               <option value="cash" <?=($moypay_r=="cash")?"selected":""; ?>>cash</option>
	               <option value="cb"<?=($moypay_r=="cb")?"selected":""; ?>>CB</option>
	           </select>
	       </div>
	        
	        <div class="form-group">
	           <label for="accsel">Compte concerné :</label>
	           <select class="custom-select" id="accsel" name="compte" required>
	           		<?php foreach($liste_compte as $compte): ?>
	           			<?php if($compte["id"]==$compte_r):?>
	           				<option value="<?=$compte["id"];?>" selected><?=ucfirst($compte["nom_account"]);?></option>
	           			<?php else:?>
	           				<option value="<?=$compte["id"];?>"><?=ucfirst($compte["nom_account"]);?></option>
	           			<?php endif;?>
	           		<?php endforeach;?>
	           </select>
	       </div>
	        
	        <div class="form-group">
	           <label for="memo">Note :</label>
	           <textarea class="form-control" id="memo" rows="5" maxlength="255" name="memo"><?=$memo_r?></textarea>
	            <small class="text-muted">255 caractères MAX</small>
	       </div>
				
			<?php if(!$no_account_found):?>
	        <button type="submit" class="btn btn-outline-primary">valider</button>
	        <?php endif; ?>
	    </form>
	    
	</div>
</div>
</section>

<?php
require_once("./include/chunks/footer.inc.php");
?>
<?php
/* compte.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Comptes";

$log->info("Retour dans compte.php", "");

if(!userISConnected()){
header("location:./connexion.php");
}

if($GET_Data_Avail){
	if(isset($_GET["action"]) && is_numeric($_GET["action"]) && $_GET["action"]==2 && isset($_GET["id"]) && is_numeric($_GET["id"]) && !empty($_GET["id"])){
		
		try{
			$resultat=$pdo->prepare("DELETE FROM account WHERE id=:id AND id_membre=:id_membre");
			$resultat->bindParam(":id",$_GET["id"],PDO::PARAM_INT);
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
		
		if($resultat){
			$success["delete_account"]="<div class='alert alert-success text-center d-block'>Compte supprimé avec succes<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
		}
		
		
		try{
			$resultat=$pdo->prepare("DELETE FROM transactions WHERE compte=:id AND id_user=:id_user");
			$resultat->bindParam(":id",$_GET["id"],PDO::PARAM_INT);
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
	
	}
	else{
		header("location:./compte.php");
	}
}

if($POST_Data_Avail){
	if(isset($_POST["versmt"]) && is_numeric($_POST["versmt"]) && $_POST["versmt"]>0 && isset($_POST["idacc"]) && !empty($_POST["idacc"])){
		
		try{
			$resultat=$pdo->prepare("UPDATE account SET solde=solde+:versmt WHERE id=:id AND id_membre=:id_membre");
			$resultat->bindParam(":versmt",$_POST["versmt"],PDO::PARAM_STR);
			$resultat->bindParam(":id",$_POST["idacc"],PDO::PARAM_INT);
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
		
		if($resultat){
			$success["credited_account"]="<div class='alert alert-success text-center d-block'>Compte crédité avec succès!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
			try{
				$resultat=$pdo->prepare("SELECT id FROM categories WHERE nom_categorie='versement' AND id_user=:id_user");
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
				$versemtCat=$resultat->fetch();
				$log->info("versementCat",$versemtCat);
			}
			else{
				$log->error("Categorie 'versement' non trouvée","");
				die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px'>Votre compte a été corrompu. Veuillez <a href='./contact.php'>contacter le webmaster.</a></div>");
			}
			
			
			try{
				$dateversmt=date('Y-m-d');
				$resultat=$pdo->prepare("INSERT INTO transactions (id_user, date, montant, categorie, compte, moypay) VALUES (:id_user, :date, :montant, :categorie, :compte, 'cash')");
				$resultat->bindParam(":id_user",$_SESSION["user"]["id"],PDO::PARAM_INT);
				$resultat->bindParam(":montant",$_POST["versmt"],PDO::PARAM_STR);
				$resultat->bindParam(":date",$dateversmt,PDO::PARAM_STR);
				$resultat->bindParam(":categorie",$versemtCat["id"],PDO::PARAM_INT);
				$resultat->bindParam(":compte",$_POST["idacc"],PDO::PARAM_INT);
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
			$notice.="<div class='alert alert-danger text-center d-block'>Nous n'avons pas pu effectuer l'operation. Essayez de recommencer.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
		}
	}
	else{
		$notice.="<div class='alert alert-danger text-center d-block'>Veuillez saisir un chiffre entier ou décimal.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
	}
}


try{
	$resultat=$pdo->prepare("SELECT * FROM account WHERE id_membre=:id_membre");
	$resultat->bindParam(":id_membre", $_SESSION["user"]["id"]);
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

$comptes=$resultat->fetchAll(PDO::FETCH_ASSOC);
$log->info("comptes trouvés:",$comptes);

if($resultat->rowCount()!==0){
	$table_body="";
	foreach($comptes as $compte){
		$table_body.="<tr>";
		$table_body.="<th scope='row'>".$compte["id"]."</th>";
		$table_body.="<td nowrap>".ucfirst($compte["nom_account"])."</td>";
		$table_body.="<td><span class='badge badge-success' style='font-size:1.2rem; color:#000;'>".number_format($compte["solde"],2,".","")." €</span></td>";
		$table_body.="<td><form action='./compte.php' method='post'><input type='text' name='versmt' maxlength='10' dir='rtl' style='max-width:70px;font-size:0.9rem;'><input type='hidden' name='idacc' value='".$compte["id"]."' > <button type='submit' class='btn btn-outline-primary' style='vertical-align:top;'>envoi</button></form></td>";
		
		$table_body.="<td><a class='btn btn-outline-primary' href='./ajout-compte.php?id=".$compte["id"]."' title='Editer' style='vertical-align:top;'><i class='far fa-edit'></i></a></td>";
		$table_body.="<td><a class='fatype-acc btn btn-outline-danger' href='./compte.php?action=2&id=".$compte["id"]."' title='Supprimer' style='vertical-align:top;'><i class='fas fa-trash'></i></a></td>";
		$table_body.="<td><a class='btn btn-outline-primary' href='./transactions.php?action=1&id=".$compte["id"]."' title='Voir les transactions' style='vertical-align:top;'><i class='far fa-eye'></i></a></td>";
		
		$table_body.="<td><a class='btn btn-outline-primary' href='./transactions.php?action=3&id=".$compte["id"]."' title=\"Voir l'historique des versements\" style='vertical-align:top;'><i class='fas fa-history'></i></a></td></tr>";
		
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
	<h2>Gérer les <span class="compte">comptes</span><span class="compte plus"><a href="./ajout-compte.php" title="Ajouter un compte"><i class="fas fa-plus-circle"></i></a></span></h2>
	
	<?=$notice?>
	<?=(isset($success["delete_account"]))?$success["delete_account"]:"";?>
	<?=(isset($success["credited_account"]))?$success["credited_account"]:"";?>
	
	<?php if(isset($table_body)):?>
	<div class="bg-light rounded shadow-lg ovf">
		<table class="table table-borderless" id="accountList">
			<thead>
				<tr>
				<th scope="col">N°</th>
				<th scope="col" nowrap>Nom du compte</th>
				<th scope="col">Solde</th>
				<th scope="col">Versement</th>
				<th scope="col">Modifier</th>
				<th scope="col">Supprimer</th>
				<th scope="col">Transactions</th>
				
				<th scope="col">Historique</th>
				</tr>
			</thead>
			<tbody>
				<?=$table_body;?>
			</tbody>
		</table>
	</div>
	
	<?php else: ?>
	<div class="row">
		<div class="advise-user-create-account rounded col-sm-7 border border-warning bg bg-warning">
			<p> Nous n'avons trouvé aucun comptes vous appartenant...</p>
			<p>Commencez par <a href="./ajout-compte.php">créer un compte</a>
			<span class="popup1">Utilisez le menu déroulant 'Comptes' ci-dessus, puis 'Ajouter un compte'.</span></p>
		</div>
	</div>
	<?php endif; ?>

</section>

















<?php
require_once("./include/chunks/footer.inc.php");
?>
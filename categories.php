<?php
/* category.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Catégories";

$log->info("Retour dans accueil.php", "");

if(!userISConnected()){
header("location:./accueil.php");
}

if($GET_Data_Avail){
	if(isset($_GET["action"]) && is_numeric($_GET["action"]) && $_GET["action"]==2){
		
		try{
			$resultat=$pdo->prepare("DELETE FROM categories WHERE id=:id");
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
		
		try{
			$resultat=$pdo->prepare("SELECT id FROM categories WHERE nom_categorie='aucune' AND id_user=:id_user");
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
			$log->warning("Pas de catégorie 'aucune' trouvée!!",$resultat->rowCount());
			die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px'>Votre compte a été corrompu. Veuillez <a href='./contact.php'>contacter le webmaster.</a></div>");
		}
		
		$usercataucune=$resultat->fetch();
		$log->info("usercataucune",$usercataucune);
		
		try{
			$resultat=$pdo->prepare("UPDATE transactions SET categorie=:cataucuneid WHERE id_user=:id_user AND categorie=:getid");
			$resultat->bindParam(":cataucuneid",$usercataucune["id"],PDO::PARAM_INT);
			$resultat->bindParam(":id_user",$_SESSION["user"]["id"],PDO::PARAM_INT);
			$resultat->bindParam(":getid",$_GET["id"],PDO::PARAM_INT);
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
		
		$success["delete_cat"]="<div class='alert alert-success text-center d-block'>Catégorie supprimée avec succes<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
	}
	else{
		header("location:./categories.php");
	}
}



try{
	$resultat=$pdo->prepare("SELECT COUNT(*) FROM categories WHERE id_user=:id");
	$resultat->bindParam(":id", $_SESSION["user"]["id"]);
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

$user_nbr_of_cats=$resultat->fetch();

$log->info("nombre de categories du user", $user_nbr_of_cats["COUNT(*)"]);

if($user_nbr_of_cats["COUNT(*)"]!=0){

	try{
		$resultat=$pdo->prepare("SELECT * FROM categories WHERE id_user=:id_membre");
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
	
	$categories=$resultat->fetchAll(PDO::FETCH_ASSOC);
	$log->info("catégories trouvés:",$categories);
	
	if($resultat->rowCount()!==0){
		$table_body="";
		foreach($categories as $categorie){
			$table_body.="<tr>";
			$table_body.="<th scope='row'>".$categorie["id"]."</th>";
			$table_body.="<td><span class='badge badge-warning' style='font-size:1.2rem; color:#000;'>".ucfirst($categorie["nom_categorie"])."</span></td>";
			if($categorie["nom_categorie"]!="aucune" && $categorie["nom_categorie"]!="versement"){
				$table_body.="<td><a class='btn btn-outline-primary' href='./ajout-categorie.php?id=".$categorie["id"]."' title='Editer' style='vertical-align:top;'><i class='far fa-edit'></i></a></td>";
				$table_body.="<td><a class='btn btn-outline-danger fatype-cat' href='./categories.php?action=2&id=".$categorie["id"]."' title='Supprimer' style='vertical-align:top;'><i class='fas fa-trash'></i></a></td>";
			}
			else{
				$table_body.="<td></td><td></td>";
			}
			$table_body.="</tr>";	
		}
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



<?php if($user_nbr_of_cats["COUNT(*)"]===0): ?>

<section class="container">
	<h2>Gérer les <span class="categorie">catégories</span><span class="categorie plus"><a href="./ajout-categorie.php" title="Ajouter une catégorie"><i class="fas fa-plus-circle"></i></a></span></h2>
	<div class="row">
		<div class="advise-user-create-account rounded col-sm-7 border border-warning bg bg-warning">
			<div>
			<p> Nous n'avez pas encore créé de catégories.</p>
			<p>Commencez par <a href="./ajout-compte.php">créer un compte...</a>
			<span class="popup1">Utilisez le menu déroulant 'Compte' ci-dessus, puis 'Ajouter un compte'.</span></p>
			<p>Puis <a href="#" class="show-cat-form">ajoutez quelques catégories</a>
			<span class="popup1">Utilisez le menu déroulant 'Catégories' ci-dessus, puis 'Ajouter une catégorie'.</span></p>
			</div>
			
			<section id="addcat" >
				<small>Veuillez utiliser un nom sans équioque. Par exemple 'Vêtements' ou 'Courses'</small>
				<form class="formStyle1" id="newCat" action="./ajout-categorie.php" method="post" autocomplete="on">
					<fieldset>
						
						<div class="inputGroup">
							<input  id="catName" name="nom_categorie" type="text" placeholder="Nom de la catégorie (*)" maxlength="20" pattern="[A-Za-z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ\-\'\ ]{3,20}" title="3 caractères minimum" required value="<?php if(isset($nom_categorie_r)){echo $nom_categorie_r; }?>">
							<span class="fieldError"></span>
						</div>
						
						<div class="inputGroup">
				        	<input type="submit" value="Valider">
				        </div>
				        
					</fieldset>
				</form>
			</section>
			
		</div>
	</div>
</section>


<?php else: ?>

<section class="container">
	<h2>Gérer les <span class="categorie">catégories</span><span class="categorie plus"><a href="./ajout-categorie.php" title="Ajouter une catégorie"><i class="fas fa-plus-circle"></i></a></span></h2>
	<?=$notice?>
	<?=(isset($success["delete_cat"]))?$success["delete_cat"]:"";?>
	
	<?php if(isset($table_body)):?>
	<div class="bg-light rounded shadow-lg ovf">
		<table class="table table-borderless" id="accountList">
			<thead>
				<tr>
				<th scope="col">N°</th>
				<th scope="col">Nom catégorie</th>
				<th scope="col">Modifier</th>
				<th scope="col">Supprimer</th>
				</tr>
			</thead>
			<tbody>
				<?=$table_body;?>
			</tbody>
		</table>
	</div>
</section>
	<?php endif; ?>
<?php endif; ?>


<?php
require_once("./include/chunks/footer.inc.php");
?>
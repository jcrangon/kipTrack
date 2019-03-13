<?php
/* connexion.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Connexion";

$log->info("Retour dans connexion.php", "");



if(userISConnected()){
header("location:/main.php");
}

if($POST_Data_Avail){
	try{
		$resultat=$pdo->prepare("SELECT * FROM qr_user WHERE pseudo=:pseudo AND status=1");
		$resultat->bindParam(":pseudo",$_POST["pseudo"]);
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
		$notice.="<div class='alert alert-danger text-center d-block'>Identifiant / mot de passe inconnus<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
	}
	else{
		$donnee_login=$resultat->fetch(PDO::FETCH_ASSOC);
		$mdp=$_POST["mdp"].$_POST["pseudo"];
		if(Dcrypt($mdp, $donnee_login["password"])){
			$_SESSION["user"]=$donnee_login;
			$_SESSION["thread"]["title"]=$_POST["mdp"];
			unset($_SESSION["user"]["password"]);
			unset($_SESSION["user"]["hcode"]);
			unset($_SESSION["user"]["validite"]);
			//sort_mode determine la periode 1 -> 1mois, 2->3mois, 3->12 mois
			//pour la presentation des transactions
			$_SESSION["settings"]["sort_mode"]=1;
			
			// verification - creation de la catégorie 'aucune'
			try{
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
			}
			
			header("location:main.php");
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
<div class="modal modal3 fade" id="forgotmdp" tabindex="-1" role="dialog" aria-labelledby="changment de mot de passe" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="examTitle">Réinitialisation du mot de passe</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<div class="text-center displaynone noticejs mb-2"></div>
      	<div><p>Veuillez saisir l'adresse Email avec laquelle vous vous êtes inscrit(e)</p></div>
        <form id="reinitmdp">
        	<div class="form-group">
				<label class="d-block" for="emailinscription">Email:</label>
				<input type="email" class="form-control w-75" id="emailinscription" name="email" placeholder="..." aria-describedby="Email" required>
			</div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close-forgot-modal" data-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-primary sendforgotmail" >Envoyer</button>
      </div>
    </div>
  </div>
</div>
<a id="formanchor" href="#"></a>

<div class="container-fluid login-container">
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6 login-form-1">
			<h3>Connexion</h3>
			<?=(isset($notice))?$notice:"";?>
			<form action="connexion.php" method="post">
				<div class="form-group">
					<input type="text" class="form-control" id="ident" name="pseudo" placeholder="Votre Pseudo *" value="" required/>
				</div>
				<div class="form-group">
					<input type="password" class="form-control" id="" name="mdp" placeholder="Mot de Passe *" value="" required/>
				</div>
				<div class="form-group">
					<input type="submit" class="btnSubmit" value="Login" />
				</div>
				<div class="form-group">
					<a href="#" data-toggle="modal" data-target="#forgotmdp"  class="ForgetPwd">Mot de passe oublié?</a>
				</div>
				<div class="form-group text-body">
					<a href="./inscription.php" class="ForgetPwd">Nouveau Membre? <span class="NewClient">Créez votre compte.</span></a>
				</div>

			</form>
		</div>
		<div class="col-md-3"></div>
	</div>
</div>

<?php
require_once("./include/chunks/footer.inc.php");
?>
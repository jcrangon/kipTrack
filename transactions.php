<?php
/* transactions.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Transactions";

$log->info("Retour dans transactions.php", "");

if(!userISConnected()){
	header("location:./accueil.php");
}
$justoneaccount=false;
$accounthistry=false;

if(!isset($_SESSION["settings"]["sort_mode"])){
	$_SESSION["settings"]["sort_mode"]=1;
}


$periode=$_SESSION["settings"]["sort_mode"];
$datereq=setperiod($_SESSION["settings"]["sort_mode"]);

if($GET_Data_Avail){
	$period_query_str="&setperiod=";
	
	if(isset($_GET["setperiod"]) && !empty($_GET["setperiod"]) && is_numeric($_GET["setperiod"])){
		$_SESSION["settings"]["sort_mode"]=intval($_GET["setperiod"]);
		$datereq=setperiod($_SESSION["settings"]["sort_mode"]);
		$url=strval($_SERVER["REQUEST_URI"]);
		$url=str_replace("&setperiod=1","",$url);
		$url=str_replace("&setperiod=2","",$url);
		$url=str_replace("&setperiod=3","",$url);
		$url=str_replace("setperiod=1","",$url);
		$url=str_replace("setperiod=2","",$url);
		$url=str_replace("setperiod=3","",$url);
		$_SERVER["REQUEST_URI"]=$url;
		$log->info("url",$url);
		$log->info("request uri",$_SERVER["REQUEST_URI"]);
		
		try{
			$resultat=$pdo->prepare("SELECT t.id, t.date,  t.montant, t.moypay, c.nom_categorie, a.nom_account, a.id AS acc_id FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id_user AND t.date ".$datereq." ORDER BY t.date ASC");
			$resultat->bindParam(":id_user", $_SESSION["user"]["id"]);
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
	
	if(isset($_GET["action"]) && $_GET["action"]==2 && isset($_GET["id"]) && is_numeric($_GET["id"]) && !empty($_GET["id"]) && isset($_GET["montant"]) && is_numeric($_GET["montant"]) && !empty($_GET["montant"]) && isset($_GET["account"]) && is_numeric($_GET["account"]) && !empty($_GET["account"])){
		
		try{
			$resultat=$pdo->prepare("DELETE FROM transactions WHERE id=:id");
			$resultat->bindParam(":id",$_GET["id"],PDO::PARAM_INT);
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
		
		$success["delete_transac"]="<div class='alert alert-success text-center d-block'>Transaction supprimée avec succes<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times</span></button></div>";
		
		if(isset($_GET["cat"]) && $_GET["cat"]=="v"){
			$delmontant=-floatval($_GET["montant"]);
		}
		else{
			$delmontant=floatval($_GET["montant"]);
		}
		
		
		try{
			$resultat=$pdo->prepare("UPDATE account SET solde=solde+:montant WHERE id=:accid");
			$resultat->bindParam(":accid",$_GET["account"],PDO::PARAM_INT);
			$resultat->bindParam(":montant",$delmontant,PDO::PARAM_STR);
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
		
		// requete de presentation des transacs
		try{
			$resultat=$pdo->prepare("SELECT t.id, t.date,  t.montant, t.moypay, c.nom_categorie, a.nom_account, a.id AS acc_id FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id_user AND t.date ".$datereq." ORDER BY t.date ASC");
			$resultat->bindParam(":id_user", $_SESSION["user"]["id"]);
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
	elseif(isset($_GET["action"]) && $_GET["action"]==1 && isset($_GET["id"]) && is_numeric($_GET["id"]) && !empty($_GET["id"])){
		// arrivée en provenance de la page "gerer les comptes" avec un click sur 'voir les transactions' associée au compte
		$justoneaccount=true;
		try{
			$resultat=$pdo->prepare("SELECT t.id, t.date,  t.montant, t.moypay, c.nom_categorie, a.nom_account, a.id AS acc_id FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id_user AND t.compte=:id_account AND t.date ".$datereq." ORDER BY t.date ASC");
			$resultat->bindParam(":id_user", $_SESSION["user"]["id"]);
			$resultat->bindParam(":id_account",$_GET["id"],PDO::PARAM_INT);
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
	elseif(isset($_GET["action"]) && $_GET["action"]==3 && isset($_GET["id"]) && is_numeric($_GET["id"]) && !empty($_GET["id"])){
		// click sur voir l'historique des compte sur la page de gestion des comptes
		try{
			$resultat=$pdo->prepare("SELECT t.id, t.date,  t.montant, t.moypay, c.nom_categorie, a.nom_account, a.id AS acc_id FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id_user AND c.nom_categorie='versement' AND a.id=:id_account AND t.date ".$datereq." ORDER BY t.date ASC");
			$resultat->bindParam(":id_user", $_SESSION["user"]["id"]);
			$resultat->bindParam(":id_account",$_GET["id"],PDO::PARAM_INT);
		}
		catch(PDOException $e){
			$log->fatal("erreur PDO1",$e->xdebug_message);
			die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px;text-align:center;'>Une erreur est survenue. Veuillez recharger la page.</div>");
		}
		catch(Exception $e){
			$log->fatal("erreur PDO2",$e->xdebug_message);
			die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px'>Une erreur est survenue. Veuillez recharger la page.</div>");
		}
		$accounthistry=true;
	}
	else{
		if(!isset($_GET["setperiod"])){
			header("location:./compte.php");
		}
	}
}
else{
	// Pas de données GET, requete de presentation des transacs
	$period_query_str="?setperiod=";
	try{
		$resultat=$pdo->prepare("SELECT t.id, t.date,  t.montant, t.moypay, c.nom_categorie, a.nom_account, a.id AS acc_id FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id_user AND t.date ".$datereq." ORDER BY t.date ASC");
		$resultat->bindParam(":id_user", $_SESSION["user"]["id"]);
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




try{
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

$transactions=$resultat->fetchAll(PDO::FETCH_ASSOC);
$log->info("transactions trouvés:",$transactions);

if($resultat->rowCount()!==0){
	$table_body="";
	foreach($transactions as $transac){
		if($transac["nom_categorie"]=="versement"){
			$table_body.="<tr style='color:green'>";
		}
		else{
			$table_body.="<tr>";
		}
		
		$table_body.="<th scope='row'>".$transac["id"]."</th>";
		$table_body.="<td nowrap>".mysqltofrenchdate($transac["date"],2)."</td>";
		$table_body.="<td>".number_format(floatval($transac["montant"]),2,".","")."€</td>";
		$table_body.="<td nowrap>".ucfirst($transac["nom_categorie"])."</td>";
		$table_body.="<td nowrap>".ucfirst($transac["nom_account"])."</td>";
		$table_body.="<td>".$transac["moypay"]."</td>";
		
		if($transac["nom_categorie"]!="versement"){
			$table_body.="<td><a class='btn btn-outline-primary' href='./ajout-transaction.php?id=".$transac["id"]."' title='Editer' style='vertical-align:top;'><i class='far fa-edit'></i></a></td>";
		}
		else{
			$table_body.="<td>--</td>";
		}
		
		if($transac["nom_categorie"]=="versement"){
			$table_body.="<td><a class=' fatype-transac btn btn-outline-danger' href='./transactions.php?action=2&id=".$transac["id"]."&montant=".$transac["montant"]."&account=".$transac["acc_id"]."&cat=v' title='Supprimer' style='vertical-align:top;'><i class='fas fa-trash'></i></a></td>";
		}
		else{
			$table_body.="<td><a class=' fatype-transac btn btn-outline-danger' href='./transactions.php?action=2&id=".$transac["id"]."&montant=".$transac["montant"]."&account=".$transac["acc_id"]."' title='Supprimer' style='vertical-align:top;'><i class='fas fa-trash'></i></a></td>";
		}
		
		if($transac["nom_categorie"]=="versement"){
			$table_body.="<td><a class='btn btn-outline-primary'  href='./detail-transactions.php?action=2&id=".$transac["id"]."&montant=".$transac["montant"]."&account=".$transac["acc_id"]."&cat=v' title='Voir' style='vertical-align:top;'><i class='far fa-eye'></i></a></td></tr>";
		}
		else{
			$table_body.="<td><a class='btn btn-outline-primary'  href='./detail-transactions.php?action=2&id=".$transac["id"]."&montant=".$transac["montant"]."&account=".$transac["acc_id"]."' title='Voir' style='vertical-align:top;'><i class='far fa-eye'></i></a></td></tr>";
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
<section class="container">
	<h2>Registre des <span class="transactions">transactions</span><span class="transactions plus"><a href="./ajout-transaction.php" title="Ajouter une transaction"><i class="fas fa-plus-circle"></i></a></span></h2>
	<div class="row">
		<div class="col-xs-8 col-sm-8 m-auto p-3">
			<div class="d-flex flex-row justify-content-around align-items-center">
				<a class="btn <?=($_SESSION["settings"]["sort_mode"]==1)?"btn-warning":"btn-outline-warning";?>" href="<?=$_SERVER["REQUEST_URI"].$period_query_str."1";?>">1 mois</a>
				<a class="btn <?=($_SESSION["settings"]["sort_mode"]==2)?"btn-warning":"btn-outline-warning";?>" href="<?=$_SERVER["REQUEST_URI"].$period_query_str."2";?>">3 mois</a>
				<a class="btn <?=($_SESSION["settings"]["sort_mode"]==3)?"btn-warning":"btn-outline-warning";?>" href="<?=$_SERVER["REQUEST_URI"].$period_query_str."3";?>">1 an</a>
			</div>
		</div>
	</div>
	<?php if($justoneaccount && isset($table_body)): ?>
		<h4>Transaction attachées au compte : <span class='badge badge-success' style='font-size:1.2rem; color:#FFF;letter-spacing:2px'><b>'<?=$transactions[0]["nom_account"]; ?>'</b></span></h4>
	<?php endif; ?>
	
	<?php if($accounthistry && isset($table_body)): ?>
		<h4>Historique de versements du compte : <span class='badge badge-success' style='font-size:1.2rem; color:#FFF;letter-spacing:2px'><b>'<?=$transactions[0]["nom_account"]; ?>'</b></span></h4>
	<?php endif; ?>
	
	
	<?=$notice?>
	<?=(isset($success["delete_transac"]))?$success["delete_transac"]:"";?>
	<?=(isset($success["credited_account"]))?$success["credited_account"]:"";?>
	
	<?php if(isset($table_body)):?>
	<div class="bg-light rounded shadow-lg ovf">
		<table class="table table-borderless" id="accountList">
			<thead>
				<tr>
				<th scope="col">N°</th>
				<th scope="col">Date</th>
				<th scope="col">Montant</th>
				<th scope="col">Catégorie</th>
				<th scope="col">Compte</th>
				<th scope="col">Paiement</th>
				<th scope="col">Modifier</th>
				<th scope="col">Supprimer</th>
				<th scope="col">Voir</th>
				</tr>
			</thead>
			<tbody>
				<?=$table_body;?>
			</tbody>
		</table>
	</div>
	
	<?php elseif($justoneaccount): ?>
	<div class="row">
		<div class="pl-5">
			<p> Nous n'avons trouvé aucune transaction attachée à ce compte...</p>
			<p>Revenir à la  <a href="./compte.php">liste des comptes.</a>
		</div>
	</div>
	
	<?php elseif($accounthistry): ?>
	<div class="row">
		<div class="pl-5">
			<p> Nous n'avons trouvé aucun historique pour ce compte...</p>
			<p>Revenir à la  <a href="./compte.php">liste des comptes.</a>
		</div>
	</div>
	
	<?php else: ?>
	<div class="row">
		<div class="pl-5">
			<p> Nous n'avons trouvé aucune transaction vous appartenant...</p>
			<p>Ajouter une <a href="./ajout-transaction.php">transaction.</a>
		</div>
	</div>
	<?php endif; ?>

</section>

<?php
require_once("./include/chunks/footer.inc.php");
?>
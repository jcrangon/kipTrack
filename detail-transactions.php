<?php
/* category.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Détails transaction";

$log->info("Retour dans detail-transactions.php", "");

if(!userISConnected()){
	header("location:./accueil.php");
}

if($GET_Data_Avail){
	extract($_GET);
	$querystringModif="./ajout-transaction.php?id=".$id;
	if(isset ($cat)){
		$querystringDel="./transactions.php?action=2&id=".$id."&montant=".$montant."&account=".$account."&cat=v";
	}
	else{
		$querystringDel="./transactions.php?action=2&id=".$id."&montant=".$montant."&account=".$account;
	}
}

$log->info("recupération des detail de la transaction","");
try{
	$req="SELECT t.id, t.date,  t.montant, t.moypay, t.memo, c.nom_categorie, a.nom_account, a.id AS acc_id FROM transactions";
	$req.=" AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id=:id AND t.id_user=:id_user";
	$resultat=$pdo->prepare($req);
	$resultat->bindParam(":id",$id,PDO::PARAM_INT);
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

$transac=$resultat->fetch(PDO::FETCH_ASSOC);
$log->info("transac",$transac);

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
	<h2>Détails <span class="transactions">transactions</span><span class="transactions plus"><a href="./transactions.php" title="Revenir à la liste"><i class="fas fa-clipboard-list"></i></a></span></h2>
	<div class="row">
		<div class="col-8 m-auto border border-light shadow-sm text-center p-2">
			<h4>Transaction N° <span style="font-size:1.2rem;color:maroon;"><?=$transac["id"];?></span></h4>
		</div>
		
		<div class="col-8 m-auto border border-light shadow-sm text-center p-2">
			<?php if(!isset($cat)): ?>
			<a class="btn btn-outline-primary" href="<?=$querystringModif?>"><i class='far fa-edit'></i> Modifier</a>
			<?php endif; ?>
			<a class="fatype-transac btn btn-outline-danger ml-2" href="<?=$querystringDel?>"><i class='fas fa-trash'></i> Supprimer</a>
		</div>
		
		<div class="col-sm-8 m-auto p-2 ovf">
			<ul class="list-group">
			 <li class="list-group-item d-flex justify-content-between align-items-center" style="letter-spacing:2px;font-size:1.2rem;">
			    Type :
			    <span  style="padding:5px;letter-spacing:2px;font-size:1.2rem; color:purpledark;border-radius:15px; background:<?=(isset($cat))?"forestgreen":"tomato";?>;"><?=(isset($cat))?"Crédit":"Débit";?></span>
			  </li>
			  <li class="list-group-item d-flex justify-content-between align-items-center" style="letter-spacing:2px; font-size:1.2rem;">
			    Date :
			    <span style="letter-spacing:2px;font-size:1.2rem; color:purple; "><?=mysqltofrenchdate($transac["date"]);?></span>
			  </li>
			  <li class="list-group-item d-flex justify-content-between align-items-center" style="letter-spacing:2px;font-size:1.2rem;">
			    Montant :
			    <span  style="letter-spacing:2px;font-size:1.2rem; color:purple;"><?=number_format(floatval($transac["montant"]),2,".","");?> €</span>
			  </li>
			  <li class="list-group-item d-flex justify-content-between align-items-center" style="letter-spacing:2px;font-size:1.2rem;">
			    Catégorie :
			    <span  style="letter-spacing:2px;font-size:1.2rem; color:purple; "><?=$transac["nom_categorie"]?></span>
			  </li>
			  <li class="list-group-item d-flex justify-content-between align-items-center" style="letter-spacing:2px;font-size:1.2rem;">
			    Compte :
			    <span  style="letter-spacing:2px;font-size:1.2rem; color:purple; "><?=$transac["nom_account"]?></span>
			  </li>
			  <li class="list-group-item d-flex justify-content-between align-items-center" style="letter-spacing:2px;font-size:1.2rem;">
			    Paiement :
			    <span  style="letter-spacing:2px;font-size:1.2rem; color:purple;"><?=$transac["moypay"]?></span>
			  </li>
			  <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" style="letter-spacing:2px;font-size:1.2rem;">
			    Note :
			    <p class="<?=($transac["memo"]=="")?"":"scalableP";?>" style="padding:10px;font-size:0.9rem; color:purple; border-radius:8px; border:1px solid tomato; max-width:50%"><?=($transac["memo"]=="")?"Pas de note enregistrée":$transac["memo"];?></p>
			  </li>
			</ul>
			
			
		</div>
	</div>
</section>








<?php
require_once("./include/chunks/footer.inc.php");
?>
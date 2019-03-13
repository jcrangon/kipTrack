<?php
/* main.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Main";

$log->info("Retour dans main.php", "");

if(!userISConnected()){
header("location:./connexion.php?#formanchor");
}

if($GET_Data_Avail){
	if(isset($_GET["annee"]) && !empty($_GET["annee"]) && is_numeric($_GET["annee"]) && intval($_GET["annee"])<=intval(date("Y"))){
		$actYear=$_GET["annee"];
	}
	else{
		$actYear=date("Y");
	}
	
	if(isset($_GET["period"]) && !empty($_GET["period"]) && is_numeric($_GET["period"])){
		if($_GET["period"]==1){
			$_SESSION["settings"]["sort_mode"]=1;
		}
		elseif($_GET["period"]==2){
			$_SESSION["settings"]["sort_mode"]=2;
		}
		elseif($_GET["period"]==3){
			$_SESSION["settings"]["sort_mode"]=3;
		}
		else{
			$_SESSION["settings"]["sort_mode"]=1;
		}
	}
	
}

if(!isset($actYear)){
	$actYear=date("Y");
}



try{
	$resultat=$pdo->prepare("SELECT COUNT(*) FROM account WHERE id_membre=:id");
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

$user_nbr_of_account=$resultat->fetch();
$log->info("nombre de compte du user", $user_nbr_of_account["COUNT(*)"]);

if($user_nbr_of_account["COUNT(*)"]!=0){
	try{
		$resultat=$pdo->prepare("SELECT SUM(solde) AS avoir FROM account WHERE id_membre=:id_membre");
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
	
	$totalavoir=$resultat->fetch(PDO::FETCH_ASSOC);
	$total_avoir=floatval($totalavoir["avoir"]);
	
	
	try{
		$resultat=$pdo->prepare("SELECT * FROM account WHERE id_membre=:id_membre");
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

	$accounts=$resultat->fetchAll(PDO::FETCH_ASSOC);
	$log->info("accounts",$accounts);
	$table1_body="";
	foreach($accounts as $account){
		$table1_body.="<tr>";
		$table1_body.="<th scope='row'>".$account["id"]."</th>";
		$table1_body.="<td>".$account["nom_account"]."</td>";
		$table1_body.="<td><span class='badge badge-success' style='font-size:1.2rem; color:#000;'>".number_format($account["solde"],2,".","")." €</span></td>";
		$table1_body.="</tr>";
	}
	$log->info("table body",$table1_body);
	
	//--REQUETES ANALYSES
	/**********************/
	//--tableau des vrsmt mensuels
	$tabmois=array(
		"jan"   => 0,
		"fev"   => 0,
		"mar"   => 0,
		"avr"   => 0,
		"mai"   => 0,
		"jun"   => 0,
		"jui"   => 0,
		"aou"   => 0,
		"sep"   => 0,
		"oct"   => 0,
		"nov"   => 0,
		"dec"   => 0,
		);
	
	//--recuperation des versements sur l'année courante
	
	try{
		$req="SELECT SUM(t.montant) AS vrs FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id AND c.nom_categorie='versement' AND t.date LIKE '".$actYear."-%'";
		$log->info("req somme de tous les versements de l'année ".$actYear,$req);
		$resultat=$pdo->prepare($req);
		$resultat->bindParam(":id",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
	
	$totvrsmtanneecourante=$resultat->fetch();
	$totvrsmtanneecourante=round($totvrsmtanneecourante["vrs"],2);
	
	$log->info("total des versements sur l'année courante",$totvrsmtanneecourante);
	
	
	//--recuperation des depenses sur l'année courante
	try{
		$req="SELECT SUM(t.montant) AS vrs FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id AND c.nom_categorie<>'versement' AND t.date LIKE '".$actYear."-%'";
		$log->info("req somme de toutes les depenses de l'annee ".$actYear,$req);
		$resultat=$pdo->prepare($req);
		$resultat->bindParam(":id",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
	$totvdepensesanneecourante=$resultat->fetch();
	$totvdepensesanneecourante=round($totvdepensesanneecourante["vrs"],2);
	
	$log->info("total des depenses sur l'année courante",$totvdepensesanneecourante);
	
	
	//--Etablissement du bilan de l'année
	if($totvrsmtanneecourante>$totvdepensesanneecourante){
		$bilan="<div class='text-center'>Total Versements : <span class='badge badge-info bilan'>".number_format(floatval($totvrsmtanneecourante),2,".","")." €</span>";
		$bilan.=" - Total Dépenses : <span class='badge badge-danger bilan'>".number_format(floatval($totvdepensesanneecourante),2,".","")." €</span>";
		$bilan.=" - Epargne : <span class='badge badge-success bilan'>".number_format(floatval($totvrsmtanneecourante-$totvdepensesanneecourante),2,".","")." €</span>";
		$bilan.="</div>";
	}
	else{
		$bilan="<div class='text-center'>Total Versements : <span class='badge badge-info bilan'>".number_format(floatval($totvrsmtanneecourante),2,".","")." €</span>";
		$bilan.=" - Total Dépenses : <span class='badge badge-danger bilan'>".number_format(floatval($totvdepensesanneecourante),2,".","")." €</span>";
		$bilan.=" - Epargne : <span class='badge badge-danger bilan'>".number_format(floatval($totvrsmtanneecourante-$totvdepensesanneecourante),2,".","")." €</span>";
		$bilan.="</div>";
	}
	
	//--Recuperation de l'année initiale des transactions
	try{
		$req="SELECT MIN(date) AS beginyear FROM transactions WHERE id_user=:id_user";
		$resultat=$pdo->prepare($req);
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
	$beginYear=$resultat->fetch(PDO::FETCH_NUM);
	$beginYear=date("Y",strtotime($beginYear[0]));
	
	$log->info("année initiale des transactions",$beginYear);
	
	// tableau des versement par mois sur l'annee courante
	try{
		$req="SELECT t.montant AS vrsmensuel, t.date AS datevrs FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id AND c.nom_categorie='versement' AND t.date LIKE '".$actYear."-%'";
		$log->info("req versements mensuels",$req);
		$resultat=$pdo->prepare($req);
		$resultat->bindParam(":id",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
	$resvrsmensuels=$resultat->fetchAll();
	
	$log->info("tableau des versement par mois sur l'annee courante",$resvrsmensuels);
	
	$tabvrsM=$tabmois;
	
	foreach($resvrsmensuels as $vrs){
		switch(date("m",strtotime($vrs["datevrs"]))){
			case "01":
				$tabvrsM["jan"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "02":
				$tabvrsM["fev"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "03":
				$tabvrsM["mar"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "04":
				$tabvrsM["avr"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "05":
				$tabvrsM["mai"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "06":
				$tabvrsM["jun"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "07":
				$tabvrsM["jui"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "08":
				$tabvrsM["aou"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "09":
				$tabvrsM["sep"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "10":
				$tabvrsM["oct"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "11":
				$tabvrsM["nov"]+=floatval($vrs["vrsmensuel"]);
			break;
			
			case "12":
				$tabvrsM["dec"]+=floatval($vrs["vrsmensuel"]);
			break;
		}
	}
	
	$log->info("tabvrsM",$tabvrsM);
	
	//--creation de $dataPoints1
	$dataPoints1=array();
	foreach($tabvrsM as $k=>$v){
		$dataPoints1[]=array("label"=>$k, "y"=>$v);
	}
	
	$log->info("dataPoints1",$dataPoints1);
	
	
	//--Recuperation des depenses par mois sur l'annee courante
	try{
		$req="SELECT t.montant AS depmensuelle, t.date AS datedep FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id AND c.nom_categorie<>'versement' AND t.date LIKE '".$actYear."-%'";
		$resultat=$pdo->prepare($req);
		$resultat->bindParam(":id",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
	
	$resdepmensuelles=$resultat->fetchAll();
	
	$log->info("depenses par mois sur l'annee courante",$resdepmensuelles);
	
	$tabdepM=$tabmois;
	foreach($resdepmensuelles as $dep){
		switch(date("m",strtotime($dep["datedep"]))){
			case "01":
				$tabdepM["jan"]+=floatval($dep["depmensuelle"]);
			break;
			
			case "02":
				$tabdepM["fev"]+=floatval($dep["depmensuelle"]);
			break;
			
			case "03":
				$tabdepM["mar"]+=floatval($dep["depmensuelle"]);
			break;
			
			case "04":
				$tabdepM["avr"]+=floatval($dep["depmensuelle"]);
			break;
			
			case "05":
				$tabdepM["mai"]+=floatval($vrs["depmensuelle"]);
			break;
			
			case "06":
				$tabdepM["jun"]+=floatval($dep["depmensuelle"]);
			break;
			
			case "07":
				$tabdepM["jui"]+=floatval($dep["depmensuelle"]);
			break;
			
			case "08":
				$tabdepM["aou"]+=floatval($dep["depmensuelle"]);
			break;
			
			case "09":
				$tabdepM["sep"]+=floatval($dep["depmensuelle"]);
			break;
			
			case "10":
				$tabdepM["oct"]+=floatval($dep["depmensuelle"]);
			break;
			
			case "11":
				$tabdepM["nov"]+=floatval($dep["depmensuelle"]);
			break;
			
			case "12":
				$tabdepM["dec"]+=floatval($dep["depmensuelle"]);
			break;
		}
	}
	
	$log->info("tabdepM",$tabdepM);
	
	//--creation de $dataPoints2
	$dataPoints2=array();
	foreach($tabdepM as $k=>$v){
		$dataPoints2[]=array("label"=>$k, "y"=>$v);
	}
	
	$log->info("dataPoints2",$dataPoints2);
	
	
	//--recuperation des depenses totales sur les 3 periodes
	$req1="SELECT SUM(t.montant) AS mt FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id AND c.nom_categorie<>'versement' AND date ".setperiod(1);
	$req2="SELECT SUM(t.montant) AS mt FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id AND c.nom_categorie<>'versement' AND date ".setperiod(2);;
	$req3="SELECT SUM(t.montant) AS mt FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id AND c.nom_categorie<>'versement' AND date ".setperiod(3);
	
	
	try{
		$resultat=$pdo->prepare($req1);
		$resultat->bindParam(":id",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
	$montantTotDepenses1M=$resultat->fetch();
	$montantTotDepenses1M=round($montantTotDepenses1M["mt"],2);
	
	
	try{
		$resultat=$pdo->prepare($req2);
		$resultat->bindParam(":id",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
	$montantTotDepenses3M=$resultat->fetch();
	$montantTotDepenses3M=round($montantTotDepenses3M["mt"],2);
	
	
	try{
		$resultat=$pdo->prepare($req3);
		$resultat->bindParam(":id",$_SESSION["user"]["id"],PDO::PARAM_INT);
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
	$montantTotDepenses12M=$resultat->fetch();
	$montantTotDepenses12M=round($montantTotDepenses12M["mt"],2);
	
	$log->info("montant tot depenses 1 Mois",$montantTotDepenses1M);
	$log->info("montant tot depenses 3 Mois",$montantTotDepenses3M);
	$log->info("montant tot depenses 12 Mois",$montantTotDepenses12M);
	
	// recuperation du tableau des categories
	try{
		$req="SELECT * FROM categories WHERE id_user=:id_user AND nom_categorie<>'versement'";
		$resultat=$pdo->prepare($req);
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
		$log->fatal("Aucune catégorie trouvée",$e->xdebug_message);
		die("<div style='width:50%;padding:10px;margin:200px auto;background:pink;color:red;border:1px solid red;font-size:18px'>Votre compte a été corrompu. Veuillez <a href='./contact.php'>contacter le webmaster.</a></div>");
	}
	else{
		$tabCategories=$resultat->fetchAll(PDO::FETCH_ASSOC);
	}
	
	// preparation du tableau des depense selon la periode avec pourcentage
	foreach($tabCategories as $categorie){
		$req1="SELECT SUM(t.montant) AS mt FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id_user AND c.id=:idcat AND date ".setperiod(1);
		$req2="SELECT SUM(t.montant) AS mt FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id_user AND c.id=:idcat AND date ".setperiod(2);;
		$req3="SELECT SUM(t.montant) AS mt FROM transactions AS t INNER JOIN account AS a ON t.compte=a.id INNER JOIN categories AS c ON t.categorie=c.id WHERE t.id_user=:id_user AND c.id=:idcat AND date ".setperiod(3);
		
		// periode = 1M
		try{
			$resultat=$pdo->prepare($req1);
			$resultat->bindParam(":id_user",$_SESSION["user"]["id"],PDO::PARAM_INT);
			$resultat->bindParam(":idcat",$categorie["id"],PDO::PARAM_INT);
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
			$depCat=$resultat->fetch();
			$depCat=round($depCat["mt"],2);
			if($montantTotDepenses1M==0){
				$pourcent=0;
			}
			else{
				$pourcent=round($depCat*100/$montantTotDepenses1M);
			}
			$depTotcat1M[]=array(
				"id"            => $categorie["id"],
				"nom_categorie" => $categorie["nom_categorie"],
				"montant"       => $depCat,
				"pourcent"      => $pourcent,
				);
		}
		$log->info("dep tot cat 1M",$depTotcat1M);
		
		// periode 3M
		try{
			$resultat=$pdo->prepare($req2);
			$resultat->bindParam(":id_user",$_SESSION["user"]["id"],PDO::PARAM_INT);
			$resultat->bindParam(":idcat",$categorie["id"],PDO::PARAM_INT);
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
			$depCat=$resultat->fetch();
			$depCat=round($depCat["mt"],2);
			if($montantTotDepenses3M==0){
				$pourcent=0;
			}
			else{
				$pourcent=round($depCat*100/$montantTotDepenses3M);
			}
			$depTotcat3M[]=array(
				"id"            => $categorie["id"],
				"nom_categorie" => $categorie["nom_categorie"],
				"montant"       => $depCat,
				"pourcent"      => $pourcent,
				);
		}
		$log->info("dep tot cat 3M",$depTotcat3M);
		
		//periode 12M
		try{
			$resultat=$pdo->prepare($req3);
			$resultat->bindParam(":id_user",$_SESSION["user"]["id"],PDO::PARAM_INT);
			$resultat->bindParam(":idcat",$categorie["id"],PDO::PARAM_INT);
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
			$depCat=$resultat->fetch();
			$depCat=round($depCat["mt"],2);
			if($montantTotDepenses12M==0){
				$pourcent=0;
			}
			else{
				$pourcent=round($depCat*100/$montantTotDepenses12M);
			}
			$depTotcat12M[]=array(
				"id"            => $categorie["id"],
				"nom_categorie" => $categorie["nom_categorie"],
				"montant"       => $depCat,
				"pourcent"      => $pourcent,
				);
		}
		$log->info("dep tot cat 12M",$depTotcat12M);
	}
	
	switch($_SESSION["settings"]["sort_mode"]){
		case 1:
			$depTotcat=$depTotcat1M;
			$montantTotDepenses=$montantTotDepenses1M;
		break;
		
		case 2:
			$depTotcat=$depTotcat3M;
			$montantTotDepenses=$montantTotDepenses3M;
		break;
		
		case 3:
			$depTotcat=$depTotcat12M;
			$montantTotDepenses=$montantTotDepenses12M;
		break;
	}
	
	$log->info("depTotcat",$depTotcat);
	$log->info("montantTotDepenses",$montantTotDepenses);
	
	$table_body_dep="";
	
	foreach($depTotcat as $depense){
		if($depense["pourcent"]){
			if($depense["pourcent"]<=25){
				$progBarColor="bg-success";
			}
			elseif($depense["pourcent"]>25 && $depense["pourcent"]<=50){
				$progBarColor="bg-warning";
			}
			elseif($depense["pourcent"]>50){
				$progBarColor="bg-danger";
			}
			$table_body_dep.="<tr>";
			$table_body_dep.="<th scope='row'>".$depense["id"]."</th>";
			$table_body_dep.="<th scope='row'><span class='badge badge-warning' style='font-size:1.2rem; color:#000;'>".ucfirst($depense["nom_categorie"])."</span></th>";
			$table_body_dep.="<th scope='row'>".number_format(floatval($depense["montant"]),2,".","")." €</th>";
			$table_body_dep.="<th scope='row'><div class='progress'><span class='progress-bar progress-bar-striped ".$progBarColor."' role='progressbar' style='width: ".strval($depense["pourcent"])."%;color:purple' aria-valuenow='".strval($depense["pourcent"])."' aria-valuemin='0' aria-valuemax='100'>".strval($depense["pourcent"])." %</span></div></th>";
			$table_body_dep.="</tr>";
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

<?php if($user_nbr_of_account["COUNT(*)"]===0): ?>
<section class="container">
<h2>Tableau de <span class="tableauDeBord">bord</span></h2>
<div class="row">
	<div class="advise-user-create-account rounded col-sm-7 border border-warning bg bg-warning">
		<p> Nous n'avons trouvé aucun comptes vous appartenant...</p>
		<p>Commencez par <a href="./ajout-compte.php">créer un compte</a>
		<span class="popup1">Utilisez le menu déroulant 'Comptes' ci-dessus, puis 'Ajouter un compte'.</span></p>
	</div>
</div>
</section>

<?php else: ?>
<section class="container">
	<h2>Tableau de <span class="tableauDeBord">bord</span></h2>
	<h2>
		<span class="categorie plus"><a href="./ajout-categorie.php" title="Ajouter une catégorie"><i class="fas fa-plus-circle"></i></a></span>
		<span class="compte plus"><a href="./ajout-compte.php" title="Ajouter un compte"><i class="fas fa-plus-circle"></i></a></span>
		<span class="transactions plus"><a href="./ajout-transaction.php" title="Ajouter une transaction"><i class="fas fa-plus-circle"></i></a></span>
	</h2>
	
	<div class="row">
		<div class="col-xs-8 col-sm-8 p-2 m-auto">
			<div class="avoir-label">
				Avoirs restants (€):
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-8 col-sm-8 m-auto p-0">
			<div class="shadow avoirs">
				<?=number_format($total_avoir,2,".","")?>
			</div>
			<div class="tabsolde mb-5">
				<div class="bg-light rounded shadow-lg ovf pl-2 pr-2">
					<table class="table table-borderless" id="accountList">
						<thead>
							<tr>
							<th scope="col">N°</th>
							<th scope="col">Nom du compte</th>
							<th scope="col">Solde</th>
							</tr>
						</thead>
						<tbody>
							<?=$table1_body;?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div id="asection"></div>
	<div class="row">
		<div class="col-sm-8 p-2 m-auto">
			<div class="avoir-label">
				Analyse:
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-8 col-sm-8 m-auto p-3">
			<div class="d-flex flex-row justify-content-around align-items-center">
				<a class="btn <?=($_SESSION["settings"]["sort_mode"]==1)?"btn-warning":"btn-outline-warning";?>" href="./main.php?period=1#asection">1 mois</a>
				<a class="btn <?=($_SESSION["settings"]["sort_mode"]==2)?"btn-warning":"btn-outline-warning";?>" href="./main.php?period=2#asection">3 mois</a>
				<a class="btn <?=($_SESSION["settings"]["sort_mode"]==3)?"btn-warning":"btn-outline-warning";?>" href="./main.php?period=3#asection">1 an</a>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="bg-light rounded shadow-lg ovf col-sm-11 m-auto">
			<table class="table table-borderless" id="accountList">
				<thead>
					<tr>
					<th scope="col">N°</th>
					<th scope="col">Catégorie</th>
					<th scope="col">Montant</th>
					<th scope="col" nowrap>% des dépenses</th>
					</tr>
				</thead>
				<tbody>
					<?=$table_body_dep;?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="mt-4" id="bsection"></div>
	<div class="row">
		<div class="col-sm-8 p-2 m-auto">
			<div class="avoir-label">
				Bilans Annuels:
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-8 p-2 m-auto">
			<div class="text-center" style="font-size:1.1rem;">
				<?=(intval($actYear)>intval($beginYear))?"<a href='./main.php?annee=".strval(intval($actYear)-1)."#bsection'><i class='fas fa-caret-left'></i></a>":""?>
				<?=$actYear; ?>
				<?=(intval($actYear)<intval(date("Y")))?"<a href='./main.php?annee=".strval(intval($actYear)+1)."#bsection'><i class='fas fa-caret-right'></i></a>":""?>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-8 p-2 m-auto">
			<div class="text-center" style="font-size:1.1rem;">
				<?=$bilan?>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="bg-light rounded shadow-lg col m-auto" id="chartContainer" style="height: 370px; max-width: 950px;">
		
		</div>
	</div>
	
	
</section>

<?php endif; ?>

<?php
require_once("./include/chunks/footer.inc.php");
?>
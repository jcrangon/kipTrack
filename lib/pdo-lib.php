<?php
/*
* Retourne un objet PDO de connexion a une base donnée en argument
* OU FALSE si la connexion echoue
*/
function getPDO($dbdata){
	if(isset($GLOBALS["loggerref"])){$logger=$GLOBALS["loggerref"];$log=true;}else{$log=false;}
	if($log){$logger->info("Dans getPDO()","");}
	$dsn=$dbdata["driver"].":host=".$dbdata["serveur"].";port=".$dbdata["port"].";dbname=".$dbdata["base"].";charset=".$dbdata["charset"];
	$user=$dbdata["user"];
	$pwd=$dbdata["pass"];
	$options=$dbdata["options"];
	try{
		$pdo= new PDO($dsn,$user,$pwd,$options);
	}
	catch(PDOexception $e){
		if($log){$logger->fatal("",$e->getMessage());}
		if($log){$logger->fatal("",$e->xdebug_message);}
		return false;
	}
	catch(Exception $e){
		if($log){$logger->fatal("",$e->getMessage());}
		if($log){$logger->fatal("",$e->xdebug_message);}
		return false;
	}
	
	if(!is_null($pdo)){
		if($log){$logger->info("Connexion!! return : PDO Object","");}
		return $pdo;
	}
	else{
		if($log){$logger->error("Erreur PDO a renvoyé un objet NULL!! return : -1","");}
		return -1;
	}
}



/*
* Ferme la connexion passée par reference
*/
function closeCnx(&$pdo){
	$pdo=null;
	return;
}

function bind(&$stmt,$tabparam){
	foreach($tabparam as $param){
		if(isset($param[2])){
			$stmt->bindvalue($param[0],$param[1],$param[2]);
		}
		else{
			switch (true) {  
				case is_int($param[1]):  
				$type = PDO::PARAM_INT;  
				break;  
				case is_bool($param[1]):  
				$type = PDO::PARAM_BOOL;  
				break;  
				case is_null($param[1]):  
				$type = PDO::PARAM_NULL;  
				break; 
				default:  
				$type = PDO::PARAM_STR;
			}
			$stmt->bindvalue($param[0],$param[1],$type);
		}
	}
	return;
}

/*
* retourne le PDOStatement a partir de la query string
* et du tabparam qui est  un tableau de tableau contenant
* le données à binder array( array("placeholder", "value", PDO::PARAM type) )
*/
function getStmt($pdo,$query,$tabparam){
	$stmt=$pdo->prepare($query);
	if(sizeof($tabparam)>0){
		bind($stmt,$tabparam);
	}
	$stmt->execute();
	return $stmt;
}


/*
* retourne un tableau dont l'indice [0] contient un tableau contenant les noms de champs
* et l'indice [1] contient le nombre de champs
*/
function getFieldsInfos($tab){
	if(!is_array($tab)){
		return false;
	}
	$i=0;
	$tabresult=array();
	foreach($tab as $k=>$v){
		$tabresult[0][]=$k;
		$i++;
	}
	$tabresult[1]=$i;
	return $tabresult;
}


/*
* Pour les requete SELECT ET SHOW
* la fonction 'query()' retourne un tableau $data contenant:
*   - 5 tableaux si $fetchtype=PDO::FETCH_ASSOC:
*       $data["result"] = contenant le set de resultats
*       $data["rowcount"] = contenant le nombre de ligne du set de resultat
*       $data["closing] = boolean true si la memoire allouée au PDOStatement a été libérée
*       $data["fieldnames"]= un tableau contenant les nom des champs dans le set de resultat
*       $data["fieldcount"]= integer - le nombre de champs dans le set de resultat
*
*   - 3 tableaux si $fetchtype autre que PDO::FETCH_ASSOC :
*       $data["result"] = contenant le set de resultats
*       $data["rowcount"] = contenant le nombre de ligne du set de resultat
*       $data["closing] = boolean true si la memoire allouée au PDOStatement a été libérée
*
* Pour les requetes INSERT, UPDATE, DELETE
* la fontion 'query()' retourne un tableau $data contenant 1 tableaux:
*       $data["rowcount"]
*/

function query($pdo, $query, $tabparam, $stmtOnly=false, $fetchtype=PDO::FETCH_ASSOC){
	if(isset($GLOBALS["loggerref"])){$logger=$GLOBALS["loggerref"];$log=true;}else{$log=false;}
	if($log){$logger->info("Dans query()","");}
	
	try{
		$query = trim(str_replace("\r", " ", $query));
		$query=preg_replace("/\s+|\t+|\n+/", " ", $query);
		
		if($log){$logger->info("Requete ",$query);}
		
		$cleanquery = explode(" ",$query);
		$querytype = strtolower($cleanquery[0]);
		if($log){$logger->info("Requete type ",$querytype);}
		
		if ($querytype === 'select' || $querytype === 'show') {
			$stmt=getStmt($pdo,$query,$tabparam);
			if($log){$logger->info("Récuperation du statement \$stmt ",$stmt);}
			
			if($stmtOnly===true){
				if($log){$logger->info("Statement seul demandé. Retour: \$stmt ",$stmt);}
				return $stmt;
			}
			
			if($log){$logger->info("Executiondu fetchAll(fetchtype)","");}
			$data["result"]= $stmt->fetchAll($fetchtype);
			$data["rowcount"]=$stmt->rowCount();
			$data["closing"]=free_result($stmt);
			if($fetchtype===PDO::FETCH_ASSOC && $data["rowcount"]!==0){
				$tabfields=getFieldsInfos($data["result"][0]);
				if($tabfields!==false){
					$data["fieldnames"]=$tabfields[0];
					$data["fieldcount"]=$tabfields[1];
				}
			}
			if($log){$logger->info("resultat de requete \$data",$data);}
			return $data;
		}
		elseif ($querytype === 'insert' || $querytype === 'update' || $querytype === 'delete') {
			$stmt=getStmt($pdo,$query,$tabparam);
			$data["rowcount"]=$stmt->rowCount();
			if($log){$logger->info("resultat de requete \$data",$data);}
			return $data;
		} 
		elseif ($querytype === 'create') {
			$stmt=$pdo->prepare($query);
			$data["result"]= $stmt->execute();
			if($log){$logger->info("resultat de requete \$data",$data);}
			return $data;
		}
		else{
			return NULL;
		}
	}
	catch(PDOException $e){
		if($log){$logger->info("erreur PDO ",$e->getMessage());}
		return false;
	}
	if($log){$logger->info("Sortie sans erreur de query() ","");}
}


/*
* libere la memoire allouée au PDOStatement
*/
function free_result(&$stmt){
	return $stmt->closeCursor();
}


function frenchDate($mysqldate){
	setlocale(LC_TIME, 'fr_FR.utf8','fra');
	$b=utf8_encode(strftime("%d-%m-%Y",strtotime($mysqldate)));
	return $b;
}


/*
* Prend en entrée un objet pdo, la requete, le tableau de tableaux de params, la table de bdd,
* la classe CSS du tableau, le titre de caption.
* Retourne un tableau $response comprenant 2 elements:
* $response["rowcount"] = nombre d enregistrements dans la table
* response["html"] = html pour afficher la table
* OU false si la requete echoue
*/
function tableToHtml($pdo, $req,$tabparam, $table, $primKey, $cssClass='', $caption=''){
	if(isset($GLOBALS["loggerref"])){$logger=$GLOBALS["loggerref"];$log=true;}else{$log=false;}
	if($log){$logger->info("Dans tableToHTML()","");}

	if($log){$logger->info("querystring",$req);}
	
	if(strpos(strtolower($req),"select")<0){
		return false;
	}
	
	
	if($log){$logger->info("Recuperation du statement pdo","");}
	$stmt=getStmt($pdo,$req,$tabparam);
	if($stmt){
		if($log){$logger->info("Statement recupéré",$stmt);}
		if($log){$logger->info("Codage du tableau...","");}
		
		$hasId=false;
		
		$disp="<table class='".$cssClass."'><caption>".$caption."</caption>";
		
		for($i=0;$i<$stmt->columnCount();$i++){
			$fieldInfo=$stmt->getColumnMeta($i);
			if(strtolower($fieldInfo["name"])==strtolower($primKey)){
				$hasId=true;
			}
		}
		
		if($hasId){
			$disp.="<thead><tr><th></th><th></th><th></th>";
		}
		else{
			$disp.="<thead>";
		}
		
		for($i=0;$i<$stmt->columnCount();$i++){
			$fieldInfo=$stmt->getColumnMeta($i);
			$disp.="<th><a class='sortby' href='?action=sort&by=".$fieldInfo["name"]."' target='_self' title='Trier'>".$fieldInfo["name"]."</a></th>";
			
			
		}
		
		$disp.="</tr></thead>";
		
		$disp.="<tfoot><th colspan='".($stmt->columnCount()+3)."'></th></tfoot>";
		
		$disp.="<tbody>";
		
		
		$nbrHomme=0;
		$nbrFemme=0;
		while($dataRow=$stmt->fetch(PDO::FETCH_ASSOC)){
			
			$disp.="<tr>";
			
			if($hasId){
				$disp.="<td><a class='action' href='affichage_annuaire.php?action=voir&id=".$dataRow["id_annuaire"]."' target='_self' title='Voir la fiche'><i class='far fa-eye'></i></a></td>";
				$disp.="<td><a class='action' href='affichage_annuaire.php?action=edit&id=".$dataRow["id_annuaire"]."' target='_self' title='Modifier le contact'><i class='fas fa-user-edit'></i></a></td>";
				$disp.="<td><a class='action' href='affichage_annuaire.php?action=delete&id=".$dataRow["id_annuaire"]."' target='_self' title='Supprimer le contact'><i class='fas fa-trash'></i></a></td>";
			}
			
			foreach($dataRow as $k=>$v){
				if($k=="date_de_naissance"){
					if($v==null){
						$v="";
					}
					else{
						$v=frenchDate($v);
					}
				}
				if($k=="sexe"){
					if($v=="m"){
						$nbrHomme++;
					}
					else{
						$nbrFemme++;
					}
				}
				$disp.="<td>".$v."</td>";
			}
			
			$disp.="</tr>";
		}
		
		$disp.="</tbody></table>";
		if($log){$logger->info("Fin du codage du tableau",$disp);}
		
		
		$response=array(
			"rowcount"=> $stmt->rowCount(),
			"html"    => $disp,
			"nbrHomme"=> $nbrHomme,
			"nbrFemme"=> $nbrFemme
			);
		
		if($log){$logger->info("Envoi du retour de fonction : \$response",$response);}
		if($log){$logger->info("Sortie de fonction tableToHtml()","");}
		return $response;
	}
	else{
		if($log){$logger->error("Erreur lors de la recuperation du statement pdo","");}
		if($log){$logger->info("Envoi du retour de fonction : false","");}
		return false;
	}
}

?>
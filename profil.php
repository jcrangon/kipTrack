<?php
/* profil.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Profil";

$log->info("Retour dans profile.php", "");

if(!userISConnected()){
	header("location:./accueil.php");
	exit();
}

$membre=$_SESSION["user"];
$log->info("membre",$membre);
$today=frenchDateTime(mysqlnow(1),2);
$frenchDob=date("d-m-Y",strtotime($membre["date_de_naissance"]));

if($_SESSION["user"]["photo"]!="default.jpg"){
	$tab=explode("300x-",$_SESSION["user"]["photo"]);
	$profil_photo="./assets/img/user-".$_SESSION["user"]["id"]."-thumb-150x-".$tab[1];
}
else{
	$profil_photo="./assets/img/default.jpg";
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
<div class="modal modal1 fade" id="chgmdp" tabindex="-1" role="dialog" aria-labelledby="changment de mot de passe" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="examTitle">Modification du mot de passe</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<div class="text-center displaynone noticejs mb-2"></div>
        <form id="modifmdp">
        	<div class="form-group">
				<label class="d-block" for="ancienmdp">Mot de passe actuel:</label>
				<input type="password" class="form-control w-75" id="ancienmdp" placeholder="...">
			</div>
			<div class="form-group">
				<label class="d-block" for="ancienmdp">Nouveau mot de passe:</label>
				<input type="password" class="form-control w-75" id="nouvmdp1" placeholder="...">
			</div>
			<div class="form-group">
				<label class="d-block" for="ancienmdp">Confirmez le mot de passe:</label>
				<input type="password" class="form-control w-75" id="nouvmdp2" placeholder="...">
			</div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-primary savechg" >Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<div class="modal modal2 fade" id="editpfil" tabindex="-1" role="dialog" aria-labelledby="edition du profile" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModangTitle">Edition de votre profil</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<div class="text-center displaynone noticejs mb-2"></div>
      	
      	<div class="errnotice"></div>
      	
        <form action="" method=""  id="editprofil" autocomplete="on">
        	<div class="form-group">
	            <label for="ident">Pseudo: (*)</label>
	            <input type="text" class="form-control" id="ident" style="color:blue" name="pseudo" value="<?= $membre["pseudo"] ?>" aria-describedby="login" placeholder="Pseudo" maxlength="30"   title="3 à 30 caractères sans espaces ni caractères spéciaux." required>
	            <small id="passwordHelpBlock" class="form-text text-muted">
	                3 à 20 caractères sans espaces ni caractères spéciaux.
	            </small>
	            <span class="FieldError"></span>
	        </div>
	        
	        <div class="form-group">
	            <label for="lname">Nom: (*)</label>
	            <input type="text" class="form-control" id="lname" style="color:blue" name="nom" value="<?=$membre["nom"] ?>" maxlength="30" aria-describedby="last name" placeholder="Nom"  title="3 caractères minimum" required>
	            <small id="passwordHelpBlock" class="form-text text-muted">
	                3 caractères au minimum.
	            </small>
	            <span class="FieldError"></span>
	        </div>
	
	        <div class="form-group">
	            <label for="fname">Prénom: (*)</label>
	            <input type="text" class="form-control" id="fname" style="color:blue" name="prenom" value="<?=$membre["prenom"] ?>" maxlength="30" aria-describedby="first name" placeholder="Prénom"  title="3 caractères minimum" required>
	            <small id="passwordHelpBlock" class="form-text text-muted">
	                3 caractères au minimum.
	            </small>
	            <span class="FieldError"></span>
	        </div>
        	
        	<div class="form-group">
	            <label for="dob">Date de naissance: (*)</label>
	            <input type="date" class="form-control" id="dob" style="color:blue" name="date_de_naissance" value="<?=$membre["date_de_naissance"] ?>" aria-describedby="date de naissance" placeholder="Date de naissance" required>
	            <span class="FieldError"></span>
	        </div>
	        
	        <div class="form-group">
	            <label for="mail">Email: (*)</label>
	            <input type="email" class="form-control" style="color:blue" id="mail" name="email" value="<?=$membre["email"] ?>" aria-describedby="Email" placeholder="Email" required>
	            <span class="FieldError"></span>
	        </div>
	        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
        <button type="submit" class="btn btn-primary" >Enregistrer</button> 
        </form>
        
      </div>
    </div>
  </div>
</div>

<div class="modal modal4 fade" id="editphoto" tabindex="-1" role="dialog" aria-labelledby="changment de mot de passe" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="examTitle">Changer ma photo de profil</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<div class="text-center displaynone noticejs mb-2"></div>
      	
        <form id="modifphoto" enctype="multipart/form-data">
        	
        	<div class="form-group">
				<label for="pic"></label>
				<img src="./assets/img/<?=$_SESSION["user"]["photo"]?>" class="rounded-circle img-responsive d-block mb-1" alt="photo de profil actuel" width="100">
				<input class="form-control" type="file" id="pic" name="photo" aria-describedby="picture" >
				<small id="defpic" class="form-text text-muted">Fichier chargé: <?=$_SESSION["user"]["photo"]?></small>
				<small id="pict" class="form-text text-muted">2Mo max...</small>
				<span class="FieldError"></span>
			    <input type="hidden" id="photoact" name="photo_actuelle" value="<?=$_SESSION["user"]["photo"]?>">
			</div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
        <button type="submit" class="btn btn-primary savepic" >Enregistrer</button>
        </form>
        
      </div>
    </div>
  </div>
</div>

<section class="container">
	<h2>Profil de <span class="text-primary" style="text-shadow: 4px 4px 2px rgba(150, 150, 150, 1);"><?= $membre["pseudo"] ?></span></h2>
</section>

<div class="container">
<div class="row">
<div class="col-md-3  toppad  pull-right col-md-offset-3 ">
<A href="editer-profil.php" >Editer le profil</A>

<A href="./deconnexion.php" >Déconnexion</A>
<br>
<p class=" text-info"><?=$today?></p>
</div>


<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
<div class="panel panel-info">
<div class="panel-heading">
<h3 class="panel-title"><?=$membre["prenom"]?> <span style='color:orange;font-size:2.5rem; '><?=strtoupper($membre["nom"])?></span></h3>
</div>
<div class="panel-body">
<div class="row">
<div class="col-md-2 col-lg-2 " align="center"><a href="#" data-toggle="modal" data-target="#editphoto" title="changer ma photo de profil"> <img alt="User Pic" src="<?=$profil_photo?>" width="150" class="rounded-circle img-responsive"> </a></div>

<!--<div class="col-xs-10 col-sm-10 hidden-md hidden-lg"> <br>-->
<!--<dl>-->
<!--<dt>DEPARTMENT:</dt>-->
<!--<dd>Administrator</dd>-->
<!--<dt>HIRE DATE</dt>-->
<!--<dd>11/12/2013</dd>-->
<!--<dt>DATE OF BIRTH</dt>-->
<!--<dd>11/12/2013</dd>-->
<!--<dt>GENDER</dt>-->
<!--<dd>Male</dd>-->
<!--</dl>-->
<!--</div>-->
<div class=" col-md-9 col-lg-9 offset-md-1 pl-5 "> 
<table class="table table-user-information">
<tbody>
<tr>
<td>Pseudo:</td>
<td><?=$membre["pseudo"]?></td>
</tr>

<tr>
<td>Nom:</td>
<td><?=strtoupper($membre["nom"])?></td>
</tr>

<tr>
<td>Prénom</td>
<td><?=$membre["prenom"]?></td>
</tr>

<tr>
<tr>
<td>Date de naissance</td>
<td><?=$frenchDob?></td>
</tr>

<tr>
<td>Email</td>
<td><a href="mailto:<?=$membre["email"]?>"><?=$membre["email"]?></a></td>
</tr>

<tr>
<td>Compte</td>
<td><?=($membre["role"]==0)?"Membre <span style='color:gold;font-size:1.8rem;'>Gold</span>":"Admin"?></td>
</tr>
<tr>
<td>Statut</td>
<td><?=($membre["status"]==1)?"Actif":"Désactivé"?></td>
</tr>

</tbody>
</table>

<div class="idmodif">
<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#chgmdp">Modifier le mot de passe</a>
</div>
</div>
</div>
</div>
<div class="panel-footer d-flex flex-row justify-content-end mt-2" style="">
<a data-original-title="Nous contacter" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary" href="./contact.php"><i class="far fa-envelope"></i></a>

<a href="#" data-original-title="Editer le profil" data-toggle="modal" data-target="#editpfil" data-toggle="tooltip" type="button" class="btn btn-sm btn-warning ml-1"><i class="far fa-edit"></i></a>
<a href="del-user-account.php" data-original-title="Supprimer le compte" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger ml-1 acc-trash"><i class="fas fa-trash"></i></a>

</div>

</div>
</div>
</div>
</div>

<?php
require_once("./include/chunks/footer.inc.php");
?>
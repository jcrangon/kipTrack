<?php
/* accueil.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Accueil";

$log->info("Retour dans accueil.php", "");

$log->info("is connected?", userISConnected());

if(isset($_SESSION["user"])){
	$log->info("\$_SESSION user", $_SESSION["user"]);
	$log->info("\$_SESSION settings", $_SESSION["settings"]);
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
<div class="row">
<article class="col-11">
	<h2 class="display-5 pl-3" style="color:purple">Sign up now,</h2>
	<p class="pl-4">and keep track of all your expenses. <br/>
		The days where all of your money disapeared before the end of the month, are now over.<br>
		Make yourself an efficient budget and keep track of yours buyng habits. Make sure you can save a little every month.<br><br>
		Sign up today, start  adding a few categories and get your own shopping life under control!!
	</p>
</article>
</div>
</section>
<?php
require_once("./include/chunks/footer.inc.php");
?>
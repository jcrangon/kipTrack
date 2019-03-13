<?php
/* contact.php */
require_once('include/init/init.inc.php');

//**************  MAIN CODE
$page="Contact";

$log->info("Retour dans contact.php", "");



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
	<h2>Contact</h2>
</section>
<div class="container">
	<div class="row">
		
		<div class="contact-form col-sm-6 order-sm-2">
			<h1 class="title">Contactez Nous</h1>
			<h2 class="subtitle">Comment pouvons-nous vous aider?</h2>
			<div class="action-result"></div>
			
			<form action="" id="contactf" method="">
				
				<input type="text" id="name" maxlength="30" name="name" placeholder="Votre nom (*)" />
				<input type="email" id="email" maxlength="255" name="email" placeholder="Votre Email (*)" />
				<input type="tel" id="phone" name="phone" maxlength="10" placeholder="Votre n° de téléphone" pattern="/[0-9]{10}/" title="10 chiffres sans espaces."/>
				<textarea name="text" id="msg" rows="8" maxlength="255" placeholder="Votre message (*)" title="255 caractères max." ></textarea>
				<button class="btn-send sendmsg" type="button"><span class="sendmsg-span">Envoyer</span><span class="displaynone spinner"><img src="./assets/img/spinner.svg" width="30" alt="spinner"></span></button>
			</form>
			
		</div>
		
		<div class="map col-sm-6 order-sm-1">
			<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5248.231329088927!2d2.2373675871249463!3d48.87506948440965!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e665211e7b1321%3A0xc409f9d97779f011!2s6+Rue+Amp%C3%A8re%2C+92800+Puteaux!5e0!3m2!1sfr!2sfr!4v1548030006344" width="100%" height="650" frameborder="0" style="border:0" allowfullscreen></iframe>
		</div>
		
	</div>
</div>
<?php
require_once("./include/chunks/footer.inc.php");
?>
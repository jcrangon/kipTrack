<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge,chrome=1">

<title>Kiptrak<?=(isset($page))?" | ".$page:""?></title>

<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="Keywords" content="">
<meta name="Subject" content="">
<meta name="Copyright" content="Jean-Christophe Rangon">
<meta name="Author" content="Jean-Christophe Rangon">
<meta name="Publisher" content="">
<meta name="Reply-To" content="jc.rangon@gmail.com">
<meta name="Revisit-After" content="30 days">
<meta name="expires" content="never">
<meta name="Robots" content="all">
<meta name="Rating" content="general">
<meta name="Distribution" content="global">
<meta name="Geography" content="Puteaux, France, 92800">

<!-- FB + LinkedIn -->
<meta name="og:type" content="website">
<meta name="og:title" content="Template bootstrap">
<meta name="og:image" content="./image/logo-big.png">
<meta name="og:description" content="">
<meta name="og:url" content="https://">

<!-- Twitter -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@">
<meta name="twitter:title" content="Template bootstrap">
<meta name="twitter:image" content="./image/logo-big.png">
<meta name="twitter:description" content="">
<meta name="twitter:url" content="https://">


<!--        Font awsome-->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">

<!--        Bootstrap CSS-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

<!--        Page Transitions-->
<link rel="stylesheet" href="./pagetransitions/page-transitions.css">

<!--        Page CSS-->
<link rel="stylesheet" href="./css/style.css">

<!--        turbolinks-->
<script src="js/turbolinks.js"></script>
</head>

<body class="<?=$jcr_page_transition;?>">
<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->

<noscript>
	<p><strong>Attention, cette page Web nécessite que JavaScript soit activé !</strong></p>
	<p>JavaScript est un langage de programmation couramment utilisé pour créer des effets intéractifs dans les navigateurs Web.</p>
	<p>Malheureusement, il est désactivé dans votre navigateur. Veuillez l'activer pour afficher cette page.</p>
	<p><a href="https://goo.gl/koeeaJ">Comment activer JavaScript ?</a></p>   
</noscript>

<!--Site Monitor-->
<!--<div class="link-to-monitor">-->
<!--	<a href="./class/monitor.php" target="_blank">Monitor</a>-->
<!--</div>-->

<!--Page Container-->
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12 col-xl-10 p-0 m-auto">
			
<!--            Header -->
			<header>
				<div class="jumbotron">
					<div class="container-fluid">
						<div class="row filter-glass">
								<div class="col-sm-6 offset-3">
									<h1 class="display-4 mb-5">KipTrak</h1>
								</div>
								<?php if(userISConnected()): ?>
								<div class="userGreetings">
									<?="bonjour, ".$_SESSION["user"]["prenom"]."  /  ".frenchDateTime(mysqlnow(1),2);?>
								</div>
								<?php endif;?>
						</div>
					</div>

				</div>
			</header>
			
<!--            Navbar -->
			<nav class="navbar navbar-expand-md navbar-dark bg-dark rounded-bottom">
			  <div class="container">
				 <a class="navbar-brand <?=(isset($page) && $page=="Accueil")?"":"text-secondary"?>" href="./accueil.php"><i class="fas fa-home"></i></a>
				  
				  <!--
				  Si l'utilisateur n'est pas connecté on affiche les liens 'connexion' et 'inscription'
				  -->
				  <?php if(!userISConnected()):?>
					  <a class="navbar-brand <?=(isset($page) && $page=="Connexion")?"":"text-secondary"?>" href="./connexion.php">Connexion</a>
					  <a class="navbar-brand <?=(isset($page) && $page=="Inscription")?"":"text-secondary"?>" href="./inscription.php">Inscription</a>
				  <?php endif; ?>
				  
				  <!--
				  Si l'utilisateur est connecté on affiche les liens 'Main' 'Transaction' et le menu deroulant 'categories'
				  -->
				  <?php if(userISConnected()):?>
					  <a class="navbar-brand <?=(isset($page) && $page=="Main")?"":"text-secondary"?>" href="./main.php">Main</a>
					  
					  
					  <span class="dropdown">
					  <a class="navbar-brand nav-link dropdown-toggle <?=(isset($page) && ($page=="Transactions" || $page=="Ajout transaction"))?"":"text-secondary"?>" href="./category.php" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Transactions</a>
	                  <div class="dropdown-menu" aria-labelledby="dropdown01">
	                   <a class="nav-link dropdown-item <?=(isset($page) && $page=="Transactions")?"active":"text-secondary"?>" href="./transactions.php">Gerer les transactions</a>
	                   <a class="nav-link dropdown-item <?=(isset($page) && $page=="Ajout transaction")?"active":"text-secondary"?>" href="./ajout-transaction.php">Ajouter une transaction</a>
	                  </div>
	                  </span>
					  
					  
					  <span class="dropdown">
					  <a class="navbar-brand nav-link dropdown-toggle <?=(isset($page) && ($page=="Catégories" || $page=="Ajout catégorie"))?"":"text-secondary"?>" href="./category.php" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Catégories</a>
	                  <div class="dropdown-menu" aria-labelledby="dropdown01">
	                   <a class="nav-link dropdown-item <?=(isset($page) && $page=="Catégories")?"active":"text-secondary"?>" href="./categories.php">Gerer les catégories</a>
	                   <a class="nav-link dropdown-item <?=(isset($page) && $page=="Ajout catégorie")?"active":"text-secondary"?>" href="./ajout-categorie.php">Ajouter une catégorie</a>
	                  </div>
	                  </span>
				  <?php endif; ?>
				  
				  <!--
				  Menu 'hamburger'
				  -->
				 <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				 </button>
				 
				 <div class="collapse navbar-collapse" id="navbarResponsive">
					<ul class="navbar-nav ml-auto">
						
						
					   
					   <!--
					    Si l'utilisateur est connecté on affiche le menu déroulant 'comptes' et le lien 'profil'
					   -->
					   <?php if(userISConnected()):?>
					   <li class="dropdown">
					    <a class=" nav-link dropdown-toggle <?=(isset($page) && ($page=="Comptes" || $page=="Ajout compte"))?"":"text-secondary"?>" href="./compte.php" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-piggy-bank"></i> Comptes</a>
	                    <div class="dropdown-menu" aria-labelledby="dropdown01">
	                     <a class="nav-link dropdown-item <?=(isset($page) && $page=="Comptes")?"active":"text-secondary"?>" href="./compte.php">Gerer les comptes</a>
	                     <a class="nav-link dropdown-item <?=(isset($page) && $page=="Ajout compte")?"active":"text-secondary"?>" href="./ajout-compte.php">Ajouter un compte</a>
	                    </div>
	                    <?php endif; ?>
	                    
	                    <!--
						  Lien 'accueil'
						 -->
					   <li class="nav-item">
						  <a class="nav-link <?=(isset($page) && $page=="Accueil")?"active":"text-secondary"?>" href="./accueil.php"><i class="fas fa-home"></i> Accueil<span class="sr-only">(current)</span></a>
					   </li>
	                    
	                    <?php if(userISConnected()):?>
	                   </li>
						<li class="nav-item">
							<a class="nav-link <?=(isset($page) && $page=="Profil")?"active":"text-secondary"?>" href="./profil.php"><i class="fas fa-flushed"></i> Profil</a>
						</li>
					   <?php endif; ?>
					   
					   <!--
						  Lien 'contact'
					   -->
					   <li class="nav-item">
						  <a class="nav-link <?=(isset($page) && $page=="Contact")?"active":"text-secondary"?>" href="./contact.php"><i class="fas fa-feather-alt"></i> Contact</a>
					   </li>
					   
					   <!--
					    Si l'utilisateur est connecté on affiche le lien 'deconnexion'
					   -->
					   <?php if(userISConnected()):?>
						<li class="nav-item">
							<a class="nav-link text-secondary" href="./deconnexion.php"><i class="fas fa-plug"></i> Déconnexion</a>
						</li>
					   <?php endif; ?>
					</ul>
				 </div>
			  </div>
			</nav>
	
<!--            BreadCrumb -->                
			<nav aria-label="breadcrumb" role="navigation">
				<ol class="breadcrumb">
					<?php if(isset($page) && $page=="Accueil"):?>
						<li class="breadcrumb-item active"><a href="./accueil.php">Accueil</a></li>
					<?php endif; ?>
					
					<?php if(isset($page) && $page=="Inscription"):?>
						<li class="breadcrumb-item "><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item active">Inscription</li>
					<?php endif; ?>
					
					<?php if(isset($page) && $page=="Connexion"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item active">Connexion</li>
					<?php endif; ?>
					
	
					
					<?php if(isset($page) && $page=="Profil"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item active">Profil</li>
					<?php endif; ?>
					
					<?php if(isset($page) && $page=="Contact"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item active">Contact</li>
					<?php endif; ?>
					
					
					
					<?php if(isset($page) && $page=="Main"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item active">Main</li>
					<?php endif; ?>
					
					<?php if(isset($page) && $page=="Transactions"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item"><a href="./main.php">Main</a></li>
						<li class="breadcrumb-item active">Gerer Transactions</li>
					<?php endif; ?>
					
					<?php if(isset($page) && $page=="Ajout transaction"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item"><a href="./main.php">Main</a></li>
						<li class="breadcrumb-item"><a href="./transactions.php">Gérer Transactions</a></li>
						<li class="breadcrumb-item active"> Ajout transaction</li>
					<?php endif; ?>
					
					<?php if(isset($page) && $page=="Détails transaction"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item"><a href="./main.php">Main</a></li>
						<li class="breadcrumb-item"><a href="./transactions.php">Gérer Transactions</a></li>
						<li class="breadcrumb-item active"> Détails transaction</li>
					<?php endif; ?>
					
					
					<?php if(isset($page) && $page=="Catégories"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item"><a href="./main.php">Main</a></li>
						<li class="breadcrumb-item active">Gérer Catégories</li>
					<?php endif; ?>
					
					<?php if(isset($page) && $page=="Ajout catégorie"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item"><a href="./main.php">Main</a></li>
						<li class="breadcrumb-item"><a href="./categories.php">Gérer Catégories</a></li>
						<li class="breadcrumb-item active"> Ajout catégorie</li>
					<?php endif; ?>
					
					
					
					<?php if(isset($page) && $page=="Comptes"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item"><a href="./main.php">Main</a></li>
						<li class="breadcrumb-item active">Gérer Comptes</li>
					<?php endif; ?>
					
					<?php if(isset($page) && $page=="Ajout compte"):?>
						<li class="breadcrumb-item"><a href="./accueil.php">Accueil</a></li>
						<li class="breadcrumb-item"><a href="./main.php">Main</a></li>
						<li class="breadcrumb-item"><a href="./compte.php">Gerer Comptes</a></li>
						<li class="breadcrumb-item active"> Ajout compte</li>
					<?php endif; ?>
					

				</ol>
			</nav>
			
<!--            Main -->
			<main>
				
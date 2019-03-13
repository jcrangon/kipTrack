/*jshint undef:true*/
/*jshint unused:false */
/*jshint esnext:true */
/*eslint no-unused-vars:off */
/*globals $:false */
/*globals alert:false */
/*globals document:false */
/*globals confirm:false */
/*globals console:false */
/*globals window:false */
/* ********************* */

'use strict';

$(document).ready(function() {

	$(".show-cat-form").on("click", function(e) {
		$("#addcat").fadeIn(1000);
	});

	$(".fatype-acc").on("click", function(e) {
		var resp = confirm("Cette action entrainera la suppression de toutes les transactions associées à ce compte. Etes-vous certain de vouloir continuer");
		if (!resp) {
			e.preventDefault();
			e.stopPropagation();
		}
	});

	$(".fatype-cat").on("click", function(e) {
		var resp = confirm("Cette action entrainera la suppression de la liaison de toutes les transactions associées à cette catégorie. Essayez de mofifier cette ctégorie plutot que de la supprimer. Etes-vous certain de vouloir continuer?");
		if (!resp) {
			e.preventDefault();
			e.stopPropagation();
		}
	});
	
	$(".fatype-transac").on("click", function(e) {
		var resp = confirm("La suppression d'une transaction re-crédite le compte du montant de celle-ci. Etes-vous certain de vouloir continuer?");
		if (!resp) {
			e.preventDefault();
			e.stopPropagation();
		}
	});
	
	
	//profil
	$(".acc-trash").on("click", function(e) {
		var resp = confirm("Cette action va entrainer la suppression de TOUTES vos données du site. Êtes vous certain(e) de vouloir continuer?");
		if (!resp) {
			e.preventDefault();
			e.stopPropagation();
		}
	});
	
	
	
	//contact
	 var panels = $('.user-infos');
    var panelsButton = $('.dropdown-user');
    panels.hide();

    //Click dropdown
    panelsButton.click(function() {
        //get data-for attribute
        var dataFor = $(this).attr('data-for');
        var idFor = $(dataFor);

        //current button
        var currentButton = $(this);
        idFor.slideToggle(400, function() {
            //Compvared slidetoggle
            if(idFor.is(':visible'))
            {
                currentButton.html('<i class="glyphicon glyphicon-chevron-up text-muted"></i>');
            }
            else
            {
                currentButton.html('<i class="glyphicon glyphicon-chevron-down text-muted"></i>');
            }
        });
    });


    $('[data-toggle="tooltip"]').tooltip();


	// bouton enregistrer le nouveau mot de passe
	$(".modal1").on("hidden.bs.modal", function(e){
		$("#ancienmdp").val("");
		$("#nouvmdp1").val("");
		$("#nouvmdp2").val("");
		$(".noticejs").fadeOut(100);
	});
	
	
	$(".savechg").on("click", function(e){
		var ancien=$("#ancienmdp").val();
		var nouv1=$("#nouvmdp1").val();
		var nouv2=$("#nouvmdp2").val();
		var mask=/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/;
		$(".noticejs").fadeOut(100);

		if(ancien==="" || nouv1==="" || nouv2===""){
			$(".noticejs").html("<div class='alert alert-danger'>Tous les champs sont obligatoires!</div>").fadeIn(1000);
		}
		else if (nouv1!==nouv2){
			$(".noticejs").html("<div class='alert alert-danger'>Les mots de passe ne sont pas identiques!</div>").fadeIn(1000);
		}
		else if(!mask.test(nouv1)){
			$(".noticejs").html("<div class='alert alert-danger'>Le mot de passe doit comporter entre 8 et 15 caractère avec au moins une majuscule, une minuscule, un chiffre et un caractere special</div>").fadeIn(1000);
		}
		else if(!mask.test(nouv2)){
			$(".noticejs").html("<div class='alert alert-danger'>Le mot de passe doit comporter entre 8 et 15 caractère avec au moins une majuscule, une minuscule, un chiffre et un caractere special</div>").fadeIn(1000);
		}
		else{
			
			$.ajax({
				url : './ajax/chgpwd.php',
				type : 'POST',
				data: {
					ancien:ancien,
					nouv1:nouv1,
					nouv2:nouv2,
				},
				dataType : "json",
		
				success : function(response, statut){
					console.log(response);
					switch (response.status){
						
						case "ok":
							$(".noticejs").html("<div class='alert alert-success'>"+response.payload+"</div>").fadeIn(1000);
						break;
						
						case "error":
							$(".noticejs").html("<div class='alert alert-danger'>"+response.payload+"</div>").fadeIn(1000);
						break;
						
						case "sessionExpire":
							window.location.assign("./deconnexion.php");
						break;
						
						case "badoldpwd":
							$(".noticejs").html("<div class='alert alert-danger'>Mot de passe actuel saisi ne correspond pas!</div>").fadeIn(1000);
						break;
						
						default:
							$(".noticejs").html("<div class='alert alert-danger'>Erreur inconnue. Essayez de recommencer</div>").fadeIn(1000);
					}
				},
		
				error : function(resultat, statut, erreur){
					alert(statut);
					alert(erreur);
					alert(resultat);
				},
		
				complete : function(resultat, statut){
		
				}
		
			});
		}
	});
	
	
	
	// formulaire de contact
	$("#name").on("keyup",function(e){
		if($(this).hasClass("border-red")){
			$(this).removeClass("border-red");
		}
		$(".sendmsg-span").html("Envoyer").fadeIn(500);
	});
	
	$("#email").on("keyup",function(e){
		if($(this).hasClass("border-red")){
			$(this).removeClass("border-red");
		}
		$(".sendmsg-span").html("Envoyer").fadeIn(500);
	});
	
	$("#msg").on("keyup",function(e){
		if($(this).hasClass("border-red")){
			$(this).removeClass("border-red");
		}
		$(".sendmsg-span").html("Envoyer").fadeIn(500);
	});
	
	$("#phone").on("keyup",function(e){
		if($(this).hasClass("border-red")){
			$(this).removeClass("border-red");
		}
		$(".sendmsg-span").html("Envoyer").fadeIn(500);
	});
	
	$(".sendmsg").on("click",function(e){
		e.preventDefault();
		e.stopPropagation();
		var  nom=$("#name").val();
		var  email=$("#email").val();
		var  phone=$("#phone").val();
		var  msg=$("#msg").val();
		
		var email_mask=/[A-Za-z0-9._%+-]{1,}@[a-zA-Z]{1,}([.]{1}[a-zA-Z]{2,}|[.]{1}[a-zA-Z]{2,}[.]{1}[a-zA-Z]{2,})/g;
		var nom_mask=/^([a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30})+([\'\s-]{0,1}[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}){0,4}$/g;
		var phone_mask=/^[0-9]{10}$/g;
		
		if(nom==="" || email==="" || msg===""){
			$(".action-result").html("<span class='alert alert-danger'>Veuillez remplir les champs obligatoires</span>").fadeIn(500);
			if(nom===""){
				$("#name").toggleClass("border-red");
				$(".sendmsg-span").html("<span class='alert alert-danger'>ERROR!!</span>").fadeIn(500);
			}
			if(email===""){
				$("#email").toggleClass("border-red");
				$(".sendmsg-span").html("<span class='alert alert-danger'>ERROR!!</span>").fadeIn(500);
			}
			if(msg===""){
				$("#msg").toggleClass("border-red");
				$(".sendmsg-span").html("<span class='alert alert-danger'>ERROR!!</span>").fadeIn(500);
			}
		}
		else if(!nom_mask.test(nom)){
			$(".action-result").html("<span class='alert alert-danger'>Veuillez saisir un nom valide</span>").fadeIn(500);
			$(".sendmsg-span").html("<span class='alert alert-danger'>ERROR!!</span>").fadeIn(500);
			$("#name").toggleClass("border-red");
		}
		else if(!email_mask.test(email)){
			$(".action-result").html("<span class='alert alert-danger'>Veuillez saisir un email valide</span>").fadeIn(500);
			$(".sendmsg-span").html("<span class='alert alert-danger'>ERROR!!</span>").fadeIn(500);
			$("#email").toggleClass("border-red");
		}
		else if(phone!=="" && !phone_mask.test(phone) ){
			$(".action-result").html("<span class='alert alert-danger'>Veuillez saisir un numéro de téléphone valide</span>").fadeIn(500);
			$(".sendmsg-span").html("<span class='alert alert-danger'>ERROR!!</span>").fadeIn(500);
			$("#phone").toggleClass("border-red");
		}
		else{
			$(".spinner").fadeIn(500);
			
			$.ajax({
				url : './ajax/contact.php',
				type : 'POST',
				data: {
					nom:nom,
					email:email,
					phone:phone,
					msg:msg,
				},
				dataType : "json",
		
				success : function(response, statut){
					console.log(response);
					switch (response.status){
						case "ok":
							$(".spinner").fadeOut(100);
							$(".action-result").html("<div class='alert alert-success'>"+response.notice+"</div>").fadeIn(500);
							$(".sendmsg-span").html("<span style='color:green'>Envoyé!!</span>").fadeIn(500);
							$("#name").val("");
							$("#email").val("");
							$("#phone").val("");
							$("#msg").val("");
						break;
						case "error":
							$(".spinner").fadeOut(100);
							$(".action-result").html("<span class='alert alert-danger'>"+response.notice+"</span>").fadeIn(500);
							$(".sendmsg-span").html("<span class='alert alert-danger'>ERROR!!</span>").fadeIn(500);
						break;
						
						case "tempUnavail":
							$(".spinner").fadeOut(100);
							$(".action-result").html("<span class='alert alert-danger'>Problème technique temporaire. Veuillez ré-essayer dans quelques instants</span>").fadeIn(500);
							$(".sendmsg-span").html("<span class='alert alert-danger'>ERROR!!</span>").fadeIn(500);
						break; 
					}
				},
		
				error : function(resultat, statut, erreur){
					alert(statut);
					alert(erreur);
					alert(resultat);
				},
		
				complete : function(resultat, statut){
		
				}
			
			});
		}
	});
	
	
	// mot de passe oublié
	$(".close-forgot-modal").on("click",function(e){
		$("#emailinscription").val("");
		$(".noticejs").empty().fadeOut(500);
		if($("#emailinscription").hasClass("border-red")){
			$("#emailinscription").removeClass("border-red");
		}
	});
	
	$("#emailinscription").on("keyup",function(e){
		if($(this).hasClass("border-red")){
			$(this).removeClass("border-red");
		}
	});
	
	$(".sendforgotmail").on("click", function(e){
		var  email=$("#emailinscription").val();
		var email_mask=/[A-Za-z0-9._%+-]{1,}@[a-zA-Z]{1,}([.]{1}[a-zA-Z]{2,}|[.]{1}[a-zA-Z]{2,}[.]{1}[a-zA-Z]{2,})/g;
		
		if(email===""){
			$("#emailinscription").toggleClass("border-red");
			$(".noticejs").html("<span class='alert alert-danger'>Veuillez renseigner le champs 'email'</span>").fadeIn(500);
		}
		else if(!email_mask.test(email)){
			$("#emailinscription").toggleClass("border-red");
			$(".noticejs").html("<span class='alert alert-danger'>Veuillez saisir un email valide</span>").fadeIn(500);
		}
		else{
			$.ajax({
				url : './ajax/fgtpwd.php',
				type : 'POST',
				data: {
					email:email,
				},
				dataType : "json",
		
				success : function(response, statut){
					console.log(response);
					switch (response.status){
						case "ok":
							$(".noticejs").html("<div class='alert alert-success'>"+response.notice+"</div>").fadeIn(500);
						break;
						case "error":
							$(".noticejs").html("<span class='alert alert-danger'>"+response.notice+"</span>").fadeIn(500);
						break;
						case "warning":
							$(".noticejs").html("<span class='alert alert-warning'>"+response.notice+"</span>").fadeIn(500);
						break;
					}
				},
		
				error : function(resultat, statut, erreur){
					alert(statut);
					alert(erreur);
					alert(resultat);
				},
		
				complete : function(resultat, statut){
		
				}
			
			});
		}
	
	});
	
	
	
	
	//edition du profil
	// bouton enregistrer le nouveau mot de passe
	
	$("#ident").on("keyup",function(e){
		if($(this).hasClass("border-red")){
			$(this).toggleClass("border-red");
		}
	});
	$("#lname").on("keyup",function(e){
		if($(this).hasClass("border-red")){
			$(this).toggleClass("border-red");
		}
	});
	$("#fname").on("keyup",function(e){
		if($(this).hasClass("border-red")){
			$(this).toggleClass("border-red");
		}
	});
	$("#dob").on("keyup",function(e){
		if($(this).hasClass("border-red")){
			$(this).toggleClass("border-red");
		}
	});
	$("#mail").on("keyup",function(e){
		if($(this).hasClass("border-red")){
			$(this).toggleClass("border-red");
		}
	});
	
	
	//edition du profil
	// bouton jaune 'editer le profil'
	$("#editprofil").on("submit",function(e){
		e.preventDefault();
		e.stopPropagation();
		
		var pseudo=$("#ident").val();
		var nom=$("#lname").val();
		var prenom=$("#fname").val();
		var dob=$("#dob").val();
		var email=$("#mail").val();
		var photoact=$("#photoact").val();
		
		var email_mask=/[A-Za-z0-9._%+-]{1,}@[a-zA-Z]{1,}([.]{1}[a-zA-Z]{2,}|[.]{1}[a-zA-Z]{2,}[.]{1}[a-zA-Z]{2,})/g;
		var nom_mask=/^([a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30})+([\'\s-]{0,1}[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}){0,4}$/g;
		var prenom_mask=/^([a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{3,30})+([\'\s-]{0,1}[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]{1,30}){0,4}$/g;
		var pseudo_mask=/^[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ\.\-\_]{3,}$/g;

		
		if(pseudo==="" || nom==="" || prenom==="" || dob==="" || email===""){
			$(".errnotice").html("<div class='alert alert-danger'>Veuillez renseigner les champs obligatoires.</div>").fadeIn(1000);
			
			if(pseudo===""){
				$("#ident").removeClass("border-red").addClass("border-red");
				$("#ident").nextAll(".FieldError").text("Champs Obligatoire!");
			}
			
			if(nom===""){
				$("#lname").removeClass("border-red").addClass("border-red");
				$("#lname").nextAll(".FieldError").text("Champs Obligatoire!");
			}
			
			if(prenom===""){
				$("#fname").removeClass("border-red").addClass("border-red");
				$("#fname").nextAll(".FieldError").text("Champs Obligatoire!");
			}
			
			if(dob===""){
				$("#dob").removeClass("border-red").addClass("border-red");
				$("#dob").nextAll(".FieldError").text("Champs Obligatoire!");
			}
			
			if(email===""){
				$("#mail").removeClass("border-red").addClass("border-red");
				$("#mail").nextAll(".FieldError").text("Champs Obligatoire!");
			}
		}
		else if(!pseudo_mask.test(pseudo)){
			$(".errnotice").html("<div class='alert alert-danger'>Erreur dans le formulaire.</div>").fadeIn(1000);
			$("#ident").removeClass("border-red").addClass("border-red");
			$("#ident").nextAll(".FieldError").text("Le champs 'Pseudo' doit contenir au minimum 3 caractères et au maximum 30 caractères. Les tirets '-', underscores '_' ainsi que les points '.' sont autorisés.!");
		}
		
		else if(!nom_mask.test(nom)){
			$(".errnotice").html("<div class='alert alert-danger'>Erreur dans le formulaire.</div>").fadeIn(1000);
			$("#lname").removeClass("border-red").addClass("border-red");
			$("#lname").nextAll(".FieldError").text("Le champs 'Nom' doit contenir au minimum 3 caractères et au maximum 30 caractères. Les espaces, les tirets '-' ainsi que les apostrophes (') sont autorisées");
		}
		
		else if(!prenom_mask.test(prenom)){
			$(".errnotice").html("<div class='alert alert-danger'>Erreur dans le formulaire.</div>").fadeIn(1000);
			$("#fname").removeClass("border-red").addClass("border-red");
			$("#fname").nextAll(".FieldError").text("Le champs 'Prénom' doit contenir au minimum 3 caractères et au maximum 30 caractères. Les espaces, les tirets '-' ainsi que les apostrophes (') sont autorisées");
		}
		
		else if(!email_mask.test(email)){
			$(".errnotice").html("<div class='alert alert-danger'>Erreur dans le formulaire.</div>").fadeIn(1000);
			$("#mail").removeClass("border-red").addClass("border-red");
			$("#mail").nextAll(".FieldError").text("Email invalide!");
		}
		else{

			$.ajax({
				url : './ajax/editprofile.php',
				type : 'POST',
				data:{
					pseudo:pseudo,
					nom:nom,
					prenom:prenom,
					dob:dob,
					email:email,
					photoact:photoact,
				},
				dataType : "json",
				success : function(response, statut){
					console.log(response);
					switch (response.status){
						case "ok":
							window.location.assign("./profil.php");
						break;
						case "error":
							$(".errnotice").html("<div class='alert alert-danger'>Erreur dans le formulaire.</div>").fadeIn(1000);
							$("#"+response.fieldID).removeClass("border-red").addClass("border-red");
							$("#"+response.fieldID).nextAll(".FieldError").text(response.payload);
						break;
						case "errorSQL":
							$(".errnotice").html("<div class='alert alert-danger'>"+response.payload+"</div>").fadeIn(1000);
						break;
						case "sessionExpire":
							window.location.assign("./update-session.php");
						break;
					}
				},
		
				error : function(resultat, statut, erreur){
					alert(statut);
					alert(erreur);
					alert(resultat);
				},
		
				complete : function(resultat, statut){
		
				}
			
			});
		}
		
	});
	
	
	//edition du profil
	//changement de la photo de profil
	$("#modifphoto").on("submit",function(e){
		e.preventDefault();
		e.stopPropagation();
		
		$.ajax({
			url : './ajax/chgpic.php',
			type : 'POST',
			data: new FormData(this),
			dataType : "json",
			cache: false,
			contentType: false,
			processData: false,
			enctype:'multipart/form-data',
			success : function(response, statut){
				console.log(response);
				switch (response.status){
					case "ok":
						window.location.assign("./profil.php");
					break;
					case "error":
						$(".errnotice").html("<div class='alert alert-danger'>Erreur dans le formulaire.</div>").fadeIn(1000);
						$("#"+response.fieldID).removeClass("border-red").addClass("border-red");
						$("#"+response.fieldID).nextAll(".FieldError").text(response.payload);
					break;
					case "errorSQL":
						$(".errnotice").html("<div class='alert alert-danger'>"+response.payload+"</div>").fadeIn(1000);
					break;
					case "sessionExpire":
						window.location.assign("./update-session.php");
					break;
				}
			},
	
			error : function(resultat, statut, erreur){
				alert(statut);
				alert(erreur);
				alert(resultat);
			},
	
			complete : function(resultat, statut){
	
			}
		
		});
	
	});

});



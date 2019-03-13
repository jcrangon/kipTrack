/*jshint undef:true*/
/*jshint unused:false */
/*jshint esnext:true */
/*eslint no-unused-vars:off */
/*globals $:false */
/*globals alert:false */
/*globals document:false */
/* ********************* */

'use strict';

$(document).ready(function() {

	$(".show-cat-form").on("click", function(e) {
		$("#addcat").fadeIn(1000);
	});

	$(".fatype-acc").on("click", function(e) {
		let resp = confirm("Cette action entrainera la suppression de toutes les transactions associées à ce compte. Etes-vous certain de vouloir continuer");
		if (!resp) {
			e.preventDefault();
			e.stopPropagation();
		}
	});

	$(".fatype-cat").on("click", function(e) {
		let resp = confirm("Cette action entrainera la suppression de la liaison de toutes les transactions associées à cette catégorie. Essayez de mofifier cette ctégorie plutot que de la supprimer. Etes-vous certain de vouloir continuer?");
		if (!resp) {
			e.preventDefault();
			e.stopPropagation();
		}
	});
});
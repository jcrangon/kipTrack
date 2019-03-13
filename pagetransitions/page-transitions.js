/*jshint undef:true*/
/*jshint unused:false */
/*jshint esnext:true */
/*eslint no-unused-vars:off */
/*globals $:false */
/*globals alert:false */
/*globals document:false */
/* ********************* */

'use strict';
function getTransitionNumber(initClass){ // effet de transition entre les pages
	return initClass.substr(initClass.length-1,1);
}

$(document).ready(function() {
	//effet de transition entre les reloads de pages
	let class_name_root="jcr_transition_set";
	let bodyReadyClass,initClass,classArray,i,activeStartClass;

	classArray=document.querySelector('body').classList;
	
	for(let i=0;i<classArray.length;i++){
		if(classArray[i].indexOf(class_name_root)!=-1){
			activeStartClass='jcr_transition_start'+getTransitionNumber(classArray[i]);
			document.querySelector('body').classList.add(activeStartClass);
		}
	}
});
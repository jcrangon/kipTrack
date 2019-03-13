<?php
// retourne une date de la forme jj-mm-aaaa en une date mysql en fonction de l'option choisie
function mysqldate($d,$option){
	if (($option==1)) {
		$msqldate=date("Y-m-d",strtotime($d));
	}

	if (($option==2)) {
		$msqldate=date("Y-m-d H:i:s",strtotime($d));
	}
	return $msqldate;
}

// retourne la date courant en format mysql
function mysqlnow($option){
	if (($option==1)) {
		$msqldate=date("Y-m-d");
	}

	if (($option==2)) {
		$msqldate=date("Y-m-d H:i:s");
	}

	return $msqldate;
}

function frenchDate($mysqldate){
	setlocale(LC_TIME, 'fr_FR.utf8','fra');
	$b=utf8_encode(strftime("%a %d %b",strtotime($mysqldate)));
	return $b;
}

function frenchDateTime($mysqldate){
	setlocale(LC_TIME, 'fr_FR.utf8','fra');
	$b=utf8_encode(strftime("%a %d %b %H:%M:%S",strtotime($mysqldate)));
	return $b;
}

// retourne une date francaise incrémenté d'au moins 1 jour
// a partir d'une date mySQL
function incrementedate($mysqldate,$nbrjour="1"){
	$date = strtotime("+".$nbrjour." day", strtotime($date));
	return date("d-m-Y", $date);
}

?>
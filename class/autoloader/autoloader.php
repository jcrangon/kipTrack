<?php
// Class Autoloader
function chargerClasseinfolder($classe)
{
	if(file_exists('./'. $classe . '.class.php')){
		require './'. $classe . '.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
  
}

function chargerClasseinfolderb($classe)
{
	if(file_exists('../'. $classe . '.class.php')){
		require '../'. $classe . '.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
  
}

function chargerClasseinfolderc($classe)
{
	if(file_exists('../../'. $classe . '.class.php')){
		require '../../'. $classe . '.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
  
}

function chargerClasseinfolderd($classe)
{
	if(file_exists('../../../'. $classe . '.class.php')){
		require '../../../'. $classe . '.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
  
}

function chargerClasseinfoldere($classe)
{
	if(file_exists('../../../../'. $classe . '.class.php')){
		require '../../../../'. $classe . '.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
  
}

function chargerClasse($classe)
{
	if(file_exists('./class/'. $classe . '.class.php')){
		require './class/'. $classe . '.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
  
}

function chargerClasseb($classe)
{
	if(file_exists('../class/'. $classe . '.class.php')){
		require '../class/'. $classe . '.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
}

function chargerClassec($classe)
{
	if(file_exists('../../class/'. $classe . '.class.php')){
		require '../../class/'. $classe . '.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
}

function chargerClassed($classe)
{
	if(file_exists('../../../class/'. $classe . '.class.php')){
		require '../../../class/'. $classe . '.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
}

function chargerClassee($classe)
{
	if(file_exists('../../../../class/'. $classe . '.class.php')){
		require '../../../../class/'. $classe . '.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
}


spl_autoload_register('chargerClasseinfolder');
spl_autoload_register('chargerClasseinfolderb');
spl_autoload_register('chargerClasseinfolderc');
spl_autoload_register('chargerClasseinfolderd');
spl_autoload_register('chargerClasseinfoldere');
spl_autoload_register('chargerClasse');
spl_autoload_register('chargerClasseb');
spl_autoload_register('chargerClassec');
spl_autoload_register('chargerClassed');
spl_autoload_register('chargerClassee');
$autoloader=true;
?>
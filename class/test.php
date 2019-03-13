<?php
session_start();
$_SESSION=array();
// Afficher les erreurs à l'écran
ini_set('display_errors', 'On');
// Enregistrer les erreurs dans un fichier de log
ini_set('log_errors', "On");
// Nom du fichier qui enregistre les logs (attention aux droits à l'écriture)
ini_set('error_log', dirname(__FILE__).'/phperrlog.txt');
// Afficher les erreurs et les avertissements
error_reporting(E_ALL);

include("./autoloader/autoloader.php");

$config["log"]["innerlog_activate"]=1;
$config["log"]["mainlog_activate"]=0;
$config["log"]["php_error_log"]=1;

$log=new phplogger("./",1,$config["log"]["innerlog_activate"]);

$log->activate();
if($config["log"]["mainlog_activate"]==0){$log->quietmode();}
if($config["log"]["php_error_log"]==0){$log->php_quietmode();}
$sfsdfsd.="kjn"; // PHP NOTICE
$log->start(__FILE__);
$log->warning("tentative d'acces direct - redirection vers ./auth/sessdestroy2.php","",__FILE__,__LINE__);
$log->warning("\$_SESSION",$_SESSION,__FILE__,__LINE__);
$log->stop(__FILE__);
$log->kill();


?>
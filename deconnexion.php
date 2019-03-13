<?php
// deconnexion.php
require_once('include/init/init.inc.php');

if(isset($_SESSION["user"])){
	unset($_SESSION["user"]);
}
if(isset($_SESSION["settings"])){
	unset($_SESSION["settings"]);
}
if(isset($_SESSION["thread"]["title"])){
	unset($_SESSION["thread"]["title"]);
}

header("location:connexion.php?#formanchor");
exit();
?>
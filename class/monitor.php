<?php
session_start();

// includes : ////////// EDITER
include("./autoloader/autoloader.php");
//////////////////////////////////////////////////////////////////////
// configuration :
$classes=array(
	"phplogger",
	);
//
/////////////////////////////////////////////////////////////////////
// functions :
function cleanpost($post){
	$tab=array();
	foreach($post as $key=>$value){
		$tab[$key]=trim(stripslashes(strip_tags($value, ENT_QUOTES)));
	}
	return $tab;
}

// URL queries Check
if(!isset($_GET["class"]) || $_GET["class"]==""){
	$showlist=true;
	$fade=1;
}
else{
	$tab=cleanpost($_GET);
	if(in_array($tab["class"],$classes)){
		$activeclass=$tab["class"];
		$sessionvarname=$activeclass."_classloghtmlcontent";
		$showlist=false;
		$fade=0;
	}
	else{
		$showlist=true;
		$fade=1;
	}
}

// main script
$txtlogwarn=false;
$txtlogerr=false;
$phpwarn=false;
$phperr=false;

if(!$showlist){
	////////// EDITER phplogger PATH
	$obj=new phplogger("./",1,0);
	$obj->activate();
	
	if(isset($_GET["action"]) && $_GET["action"]=="clearlog"){
		$obj->txtlog_clear();
		if (isset($_SESSION[$sessionvarname])){
			$_SESSION[$sessionvarname]="";
		}
	}
	if(isset($_GET["action"]) && $_GET["action"]=="clearall"){
		$obj->txtlog_clear();
		foreach($classes as $classname){
			$sessionname=$classname."_classloghtmlcontent";
			if (isset($_SESSION[$sessionname])){
				$_SESSION[$sessionname]="";
			}
		}
	}
	$txtlog_html_content=$obj->txtlog_html_content();

	$errlog_html_content=$obj->errlog_html_content();
	
	$raw_errlog_content=strtolower($obj->errlog_txt_content());

	if (isset($_SESSION[$sessionvarname])){
		$class_log_html_content=$_SESSION[$sessionvarname];
	}
	else{
		$class_log_html_content="<span style=\"color:rgba(255,0,0,1)\">No log found ... use save_ramlog_to_session() method</span>";
	}
	
	if(strlen($raw_errlog_content)!=0){$phpwarn=true;}
	if(strpos($raw_errlog_content,"php fatal")!==false || strpos($raw_errlog_content,"php parse")!==false){$phperr=true;}
	if(strpos($txtlog_html_content,">WARNING<")!==false){$txtlogwarn=true;}
	if(strpos($txtlog_html_content,">ERROR<")!==false || strpos($txtlog_html_content,">FATAL<")!==false){$txtlogerr=true;}

}
else{
	$activeclass="";
	$txtlog_html_content="<span style=\"color:#FFFFFF\">Class not found</span>";
	$errlog_html_content="<span style=\"color:#FFFFFF\">Class not found</span>";
	$class_log_html_content="<span style=\"color:#FFFFFF\">Class not found</span>";
}


?>
<!DOCTYPE html>
<html>
<head>
	
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<link href="https://fonts.googleapis.com/css?family=Nothing+You+Could+Do" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=VT323" rel="stylesheet">

<style>
body{
  padding:0;
  margin:0;
}

.maincontainer {
	position:absolute;
	top:0;
	left:0;
	min-width: 100%;
	min-height:100%;
	max-width: 100%;
	max-height:100%;
	-webkit-background-size:cover;
	-moz-background-size:cover;
	-o-background-size:cover;
	background-size:cover;
	overflow:auto;
	/*safari*/
	background-image: -webkit-gradient(
		linear,
		left bottom,
		left top,
		color-stop(0.18, rgb(3, 73, 0)),
		color-stop(0.87, rgb(237, 244, 237))
		);
	/* Chrome,Safari4+ */
	background: -webkit-gradient(linear, left bottom, left top, color-stop(0%,rgb(3, 73, 0)),
		color-stop(70%,rgb(237, 244, 237)));
	/* Chrome10+,Safari5.1+ */
	background: -webkit-linear-gradient(bottom, rgb(3, 73, 0), rgb(237, 244, 237) 70%);

	/*mozilla*/
	background: -moz-linear-gradient(bottom, rgb(3, 73, 0), rgb(237, 244, 237) 70%);

	/* opera */
	background: -o-linear-gradient(bottom, rgb(3, 73, 0), rgb(237, 244, 237) 70%);

	/* IE 6-9 */
	background: linear-gradient(bottom,rgb(3, 73, 0), rgb(237, 244, 237) 70%);

	/* IE 10+ */
	background: -ms-linear-gradient(bottom,rgb(3, 73, 0), rgb(237, 244, 237) 70%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='rgb(3, 73, 0)', endColorstr='rgb(237, 244, 237)',GradientType=0 );

	/*tout browser */
	overflow:auto;
	/*Mozilla */
	/*overflow:-Moz-Scrollbars-None;*/
}

.top{
	width:100%;
	margin:0 auto;
	padding-top:10px;
	padding-bottom:10px;
}

.top button{
	background-color:rgba(61, 24, 86, 0.5);
	border:0;
	color:#fff;
	padding:10px;
	font-size:15px;
	cursor:pointer;
}

.center{
	width:100%;
	margin:0 auto;
	padding-top:0px;
	padding-bottom:10px;
}

.bottom{
	width:100%;
	margin:10px auto;
	padding-top:0px;
	padding-bottom:10px;
}

.log{
	float:left;
	margin-left:10px;
	padding-left:15px;
	width:65%;
	max-width:65%;
	min-height:400px;
	max-height:400px;
	overflow-y:auto;
	background-color:rgba(59, 63, 59, 0.9);
	border: 2px solid rgb(3, 73, 0);
	border-radius: 50px 0 0 20px;
}

.log:hover{
	border: 2px solid rgb(255, 0, 0);
}

.classlog{
	float:left;
	margin-left:10px;
	padding-left:15px;
	width:30%;
	max-width:30%;
	min-height:400px;
	max-height:400px;
	overflow-y:auto;
	background-color:rgba(59, 63, 59, 0.9);
	border: 2px solid rgb(3, 73, 0);
	border-radius: 50px 0 0 20px;
}

.classlog:hover{
	border: 2px solid rgb(255, 0, 0);
}

.phplog{
	float:left;
	margin-top:20px;
	margin-left:10px;
	padding-left:15px;
	width:65%;
	max-width:65%;
	min-height:140px;
	max-height:140px;
	overflow-y:auto;
	background-color:rgba(59, 63, 59, 0.9);
	border: 2px solid rgb(3, 73, 0);
	border-radius: 20px 0 0 50px;
}

.phplog:hover{
	border: 2px solid rgb(255, 0, 0);
}

.logo{
	float:left;
	margin-top:20px;
	margin-left:10px;
	padding-left:15px;
	padding-top:20px;
	width:30%;
	min-height:140px;
	max-height:140px;
	
}

.showlist-bkgd{
	position:absolute;
	top:0;
	left:0;
	width:100%;
	min-height:100%;
	z-index:100000;
	background-color:rgba(0,0,0,0.7); /* background color and transparency of the overlays*/
	display:hidden;
}

.showlist{
	margin-top:10%;
	width:40%;
	min-height:300px;
	max-height:300px;
	overflow-y:auto;
	border:1px solid rgba(19, 170, 0, 1);
	color:rgba(19, 170, 0, 1);
	font-size:30px;
	font-family: 'VT323', monospace;
	display:hidden;
}

.showlist a:link,a:visited{
	border:2px solid rgba(19, 170, 0, 1);
    color: rgba(19, 170, 0, 1);
    min-width:160px;
    padding:10px;
    margin-top:10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    -webkit-transition: background-color 1s;
    transition: background-color 1s;
    
}

.showlist a:hover, a:active {
    background-color: rgba(19, 170, 0, 1);
    color:#FFFFFF;
}

.fatal{
	position: absolute;
	left:18px;
	top:58px;
	width:20px;
	z-index:10;
}

.warning{
	position: absolute;
	left:10px;
	top:70px;
	width:20px;
	z-index:9;
}

.phpfatal{
	position: absolute;
	left:18px;
	top:468px;
	width:20px;
	z-index:10;
}

.phpwarning{
	position: absolute;
	left:10px;
	top:480px;
	width:20px;
	z-index:9;
}
</style>
<script>
// body onload
function showlist(fade){
	if(fade===1){
		FX.fadeIn(document.getElementById('overlay1'), {
	        duration: 1000,
	        complete: function() {
	        }
    	});
	}
}
// FADE IN, FADE OUT
(function() {
    var FX = {
        easing: {
            linear: function(progress) {
                return progress;
            },
            quadratic: function(progress) {
                return Math.pow(progress, 2);
            },
            swing: function(progress) {
                return 0.5 - Math.cos(progress * Math.PI) / 2;
            },
            circ: function(progress) {
                return 1 - Math.sin(Math.acos(progress));
            },
            back: function(progress, x) {
                return Math.pow(progress, 2) * ((x + 1) * progress - x);
            },
            bounce: function(progress) {
                for (var a = 0, b = 1, result; 1; a += b, b /= 2) {
                    if (progress >= (7 - 4 * a) / 11) {
                        return -Math.pow((11 - 6 * a - 11 * progress) / 4, 2) + Math.pow(b, 2);
                    }
                }
            },
            elastic: function(progress, x) {
                return Math.pow(2, 10 * (progress - 1)) * Math.cos(20 * Math.PI * x / 3 * progress);
            }
        },
        animate: function(options) {
            var start = new Date;
            var id = setInterval(function() {
                var timePassed = new Date - start;
                var progress = timePassed / options.duration;
                if (progress > 1) {
                    progress = 1;
                }
                options.progress = progress;
                var delta = options.delta(progress);
                options.step(delta);
                if (progress == 1) {
                    clearInterval(id);
                    options.complete();
                }
            }, options.delay || 10);
        },
        fadeOut: function(element, options) {
            var to = 1;
            this.animate({
                duration: options.duration,
                delta: function(progress) {
                    progress = this.progress;
                    return FX.easing.swing(progress);
                },
                complete: options.complete,
                step: function(delta) {
                    element.style.opacity = to - delta;
                }
            });
        },
        fadeIn: function(element, options) {
            var to = 0;
            this.animate({
                duration: options.duration,
                delta: function(progress) {
                    progress = this.progress;
                    return FX.easing.swing(progress);
                },
                complete: options.complete,
                step: function(delta) {
                    element.style.opacity = to + delta;
                }
            });
        }
    };
    window.FX = FX;
})()
</script>
<title>Log Monitor</title>
</head>

<body onload="showlist(<?php echo $fade; ?>);">
<?php if($showlist){ // afficher la liste des class monitorables?>
	<div class="showlist-bkgd" id="overlay1">
		<center>
			<div class="showlist" id="classlist">
				<p style="text-align:center">
					<b>Choisissez une classe à monitorer :</b>
				</p>
				<p style="text-align:center">
<?php foreach($classes as $class){ ?>
	<a  href="<?php echo "./monitor.php?class=".$class ?>" style="text-decoration:none"><?php echo $class ?></a><br/>
<?php } ?>
				</p>
			</div>
		</center>
	</div>
<?php } // fin de la liste des class monitorables?>
	<div class="maincontainer">
		<div class="top">
			<center>
				<a href='./monitor.php?class=<?php echo $activeclass; ?>&action=clearall'><button style='cursor:pointer;'>CLEAR ALL</button></a>
				<a href='./monitor.php?class=<?php echo $activeclass; ?>&action=clearlog'><button style='cursor:pointer;'>CLEAR</button></a>
				<a href='./monitor.php?class=<?php echo $activeclass; ?>'><button style='cursor:pointer;'>REFRESH</button></a>
				<a href='./monitor.php?class='><button style='cursor:pointer;'>Change Class</button></a>
			</center>
		</div>
		<div class="center">
<?php if($txtlogerr){ ?>
			<div class="fatal"><img src="./monitor pics/fatal.png" width="20px" /></div>
<?php } ?>
<?php if($txtlogwarn){ ?>
			<div class="warning"><img src="./monitor pics/warning.png" width="20px" /></div>
<?php } ?>
			<div class="log" title="Script log">
				<?php echo $txtlog_html_content; ?>
			</div>
			<div class="classlog" title="Inner class log">
				<?php echo $class_log_html_content; ?>
			</div>
		</div>
		<div class="bottom">
<?php if($phperr){ ?>
			<div class="phpfatal"><img src="./monitor pics/fatal.png" width="20px" /></div>
<?php } ?>
<?php if($phpwarn){ ?>
			<div class="phpwarning"><img src="./monitor pics/warning.png" width="20px" /></div>
<?php } ?>
			<div class="phplog" title="PHP error log">
				<?php echo $errlog_html_content; ?>
			</div>
			<div class="logo">
				<center><span style="font-family: 'Nothing You Could Do', cursive;font-size:45px;"><?php if($activeclass!=""){echo $activeclass;}else{echo "Class";} ?> MONITOR</span></center>
			</div>
		</div>
	</div>
</body>

</html>












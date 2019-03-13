<?php
// requires classInnerLogger.class.php to work

class phplogger{
	// PROPERTIES
	private $_class_name;
	private $_pathtologfolder;
	private $_logfile;
	private $_phplogfile;
	private $_phperrlogfile;
	private $_active;
	private $_globalactive;
	private $_verbosity;
	private $_colors;
	private $_debug_color;
	private $_info_color;
	private $_warning_color;
	private $_error_color;
	private $_fatal_color;
	private $_txt_color;
	private $_php_txt_color;
	private $_bkgd_color;
	private $_ramlog_color;
	private $_txtlog_font_size;
	private $_txtlog_font_family;
	private $_innerlog;
	
	// CONSTANT
	const DEBUG="#75ef40";
	const INFO="#4045ed";
	const WARNING="#f2ee1d";
	const ERROR="#ef8a07";
	const FATAL="#ea2020";
	const TXT="#cec6c6";
	const PHPTXT="#268e26";
	const BKGD="#171715";
	const RAMLOGCOLOR="rgba(255,0,0,1)";
	
	public function __construct($path,$verbose,$innerlogger=1){
		$this->setInnerlogger($innerlogger);
		$this->setColortab();
		$this->setColors($this->_colors);
		$this->init_class_name();
		$this->setPathtofolder($path);
		$this->setLogfile($this->_pathtologfolder);
		$this->setPHPerrLogfile($this->_pathtologfolder);
		$this->setVerbosity($verbose);
		$this->setFontsize();
		$this->setFontfamily();
	}
	
	
	// INITERS
	protected function init_class_name(){
		$this->_class_name="phplogger";
		$this->add_innerlog_line("----- phplogger: *** Class name Initialisation *** ");
	}
	
	// SETTERS
	protected function setInnerlogger($onoff){
		if($onoff!==0 && $onoff!==1){
			DIE("Wrong argument in class constructor (\$innerloger)");
		}
		if($onoff===1){
			$this->_innerlog=new classInnerlogger;
		}
		else{
			$this->_innerlog=null;
		}
	}
	protected function setColortab(){
		$this->_colors=array(
				"DEBUG" => SELF::DEBUG,
				"INFO" => SELF::INFO,
				"WARNING" => SELF::WARNING,
				"ERROR" => SELF::ERROR,
				"FATAL" => SELF::FATAL,
				"TXT" => SELF::TXT,
				"PHPTXT" => SELF::PHPTXT,
				"BKGD" => SELF::BKGD,
				"RAMLOGCOLOR" => SELF::RAMLOGCOLOR
			);
		$this->add_innerlog_line("----- phplogger: Colors table initialisation ");
	}
	
	public function setColors(array $tab){
		foreach($tab as $k=>$v){
			switch($k){
				case "DEBUG":
					$this->_debug_color=$v;
					$this->_colors["DEBUG"]=$v;
				break;
				case "INFO":
					$this->_info_color=$v;
					$this->_colors["INFO"]=$v;
				break;
				case "WARNING":
					$this->_warning_color=$v;
					$this->_colors["WARNING"]=$v;
				break;
				case "ERROR":
					$this->_error_color=$v;
					$this->_colors["ERROR"]=$v;
				break;
				case "FATAL":
					$this->_fatal_color=$v;
					$this->_colors["FATAL"]=$v;
				break;
				case "TXT":
					$this->_txt_color=$v;
					$this->_colors["TXT"]=$v;
				break;
				case "PHPTXT":
					$this->_php_txt_color=$v;
					$this->_colors["PHPTXT"]=$v;
				break;
				case "BKGD":
					$this->_bkgd_color=$v;
					$this->_colors["BKGD"]=$v;
				break;
				case "RAMLOGCOLOR":
					$this->_ramlog_color=$v;
					$this->_colors["RAMLOGCOLOR"]=$v;
					$this->setRAMLOGColors(array("RAMLOGCOLOR"=>$v));

				break;
			}
		}
		$this->add_innerlog_line("----- phplogger: Setting individual color variables - setColors()  = ".print_r($this->_colors,true));
	}
	
	public function setRAMLOGFontsize($fontsize="14px"){
		if(!is_null($this->_innerlog)){
			$this->_innerlog->setFontsize($fontsize);
		}
	}
	public function setRAMLOGFontfamily($fontfam="Times New Roman, Georgia, serif"){
		if(!is_null($this->_innerlog)){
			$this->_innerlog->setFontfamily($fontfam);
		}
	}
	public function setRAMLOGColors(array $colors){
		if(!is_null($this->_innerlog)){
			$this->_innerlog->setColors($colors);
		}
	}
	
	public function setFontsize($fontsize="14px"){
		$this->_txtlog_font_size=$fontsize;
		$this->add_innerlog_line("----- phplogger: Setting fontsize = ".print_r($fontsize,true));
		$this->setRAMLOGFontsize($fontsize);
	}
	
	public function setFontfamily($fontfam="Times New Roman, Georgia, serif"){
		$this->_txtlog_font_family=$fontfam;
		if(!is_null($this->_innerlog)){
			$this->setRAMLOGFontfamily($fontfam);
			$this->add_innerlog_line("----- phplogger: Setting font family = ".print_r($fontfam,true));
		}
	}
	
	protected function setGlobalactive(){
		$this->checkSession();
		if($this->_active===1){
			$this->_globalactive=1;
			$this->add_innerlog_line("----- phplogger: Global Active Activated   =");
		}
		elseif($this->_active===0){
			$this->_globalactive=0;
			$this->add_innerlog_line("----- phplogger: Global Active Deactivated  =");
		}
		else{
			DIE ("phplogger: Cannot set Global Activation...");
		}
	}
	
	protected function setLogfile($pathtofolder){
		$this->_logfile=$pathtofolder.'log.txt';
		$this->add_innerlog_line("----- phplogger: Setting txt log file = ".print_r($this->_logfile,true));
	}
	
	protected function setPHPerrLogfile($pathtofolder){
		$this->_phperrlogfile=$pathtofolder.'phperrlog.txt';
		$this->add_innerlog_line("----- phplogger: Setting php error log file = ".print_r($this->_phperrlogfile,true));
	}
	
	protected function setPathtofolder($path){
		if(!is_string($path)){
			DIE ("phplogger: Wrong path to folder argument...");
		}
		$lastchar=substr($path, -1);
		if($lastchar!="/"){$path.="/";}
		if(!file_exists($path."log.txt")){
			if(!$fh=fopen($path."log.txt","w+")){
				DIE ("Directory Not Writeable - Check permissions...");
			}
			else{
				fclose($fh);
			}
		}
		if(!file_exists($path."phperrlog.txt")){
			if(!$fh=fopen($path."phperrlog.txt","w+")){
				DIE ("Directory Not Writeable - Check permissions...");
			}
			else{
				fclose($fh);
			}
		}
		if(!$fh=fopen($path."log.txt","a")){
			DIE ("Log file Not Writeable - Check permissions...");
		}
		else{
			fclose($fh);
		}
		if(!$fh=fopen($path."phperrlog.txt","a")){
			DIE ("PHP Log file Not Writeable - Check permissions...");
		}
		else{
			fclose($fh);
		}
		$this->_pathtologfolder=$path;
		$this->add_innerlog_line("----- phplogger: Setting path to folder \$path   = ".print_r($path,true));
	}
	
	public function setVerbosity($verbose){
		if($verbose<1 || $verbose>3){
			DIE ("phplogger: Wrong verbosity argument...");
		}
		$this->_verbosity=$verbose;
		$this->add_innerlog_line("----- phplogger: Loading verbosity level = ".print_r($verbose,true));
	}
	
	
	
	
	// getters
	public function getActivationState(){
		$this->add_innerlog_line("----- phplogger: Activation state sent = ".print_r($this->_active,true));
		return $this->_active;
	}
	
	public function getVerbosityLevel(){
		$this->add_innerlog_line("----- phplogger: Verbosity state sent = ".print_r($this->_verbosity,true));
		return $this->_verbosity;
	}
	
	public function getLog_content(){
		if(!is_null($this->_innerlog)){
			return $this->_innerlog->getLog_content();
		}
		else{
			return "Inner Logger not loaded";
		}
	}
	
	public function getHTMLlog_content(){
		if(!is_null($this->_innerlog)){
			return $this->_innerlog->getHTMLlog_content();
		}
		else{
			return "Inner Logger not loaded";
		}
	}
	
	protected function getClassname(){
		$this->add_innerlog_line("----- phplogger: getClassname() - Class name sent = ".print_r($this->_class_name,true));
		return $this->_class_name;
	}
	
	public function getColors_in_use(){
		$this->add_innerlog_line("----- phplogger: getColors_in_use() - Colors table sent = ".print_r($this->_colors,true));
		return $this->_colors;
	}
	
	public function getFontsize(){
		$this->add_innerlog_line("----- phplogger: getFontsize() - font size sent = ".print_r($this->_txtlog_font_size,true));
		return $this->_txtlog_font_size;
	}
	
	public function getFontfamily(){
		$this->add_innerlog_line("----- phplogger: getFontfamily() - font family sent = ".print_r($this->_txtlog_font_family,true));
		return $this->_txtlog_font_family;
	}
	
	public function getRAMLOGFontsize(){
		if(!is_null($this->_innerlog)){
			return $this->_innerlog->getFontsize();
		}
		else{
			return false;
		}
		
	}
	
	public function getRAMLOGFontfamily(){
		if(!is_null($this->_innerlog)){
			return $this->_innerlog->getFontfamily();
		}
		else{
			return false;
		}
	}
	
	public function getRAMLOGClassname(){
		if(!is_null($this->_innerlog)){
			return $this->_innerlog->getClassname();
		}
		else{
			return false;
		}
	}
	
	public function getRAMLOGColors_in_use(){
		if(!is_null($this->_innerlog)){
			return $this->_innerlog->getColors_in_use();
		}
		else{
			return false;
		}
	}
	
	
	// PROTECTED METHODS
	protected function clean_file_location($location){
		$location=rtrim(strval($location));
		$lastchar=substr($location, -1);
		if($lastchar=="/"){$location=substr($location,0,strlen($location)-1);}
		$tabloc=explode("/",$location);
		switch(true){
			case sizeof($tabloc)>=4:
				$loc=".../".$tabloc[sizeof($tabloc)-4]."/".$tabloc[sizeof($tabloc)-3]."/".$tabloc[sizeof($tabloc)-2]."/".$tabloc[sizeof($tabloc)-1];
			break;
			case sizeof($tabloc)>=3:
				$loc=".../".$tabloc[sizeof($tabloc)-3]."/".$tabloc[sizeof($tabloc)-2]."/".$tabloc[sizeof($tabloc)-1];
			break;
			case sizeof($tabloc)>=2:
				$loc=".../".$tabloc[sizeof($tabloc)-2]."/".$tabloc[sizeof($tabloc)-1];
			break;
			default:
				$loc=".../".$tabloc[sizeof($tabloc)-1];
		}
		return $loc;
	}
	protected function clearLog_content(){
		if(!is_null($this->_innerlog)){
			$this->_innerlog->clearLog_content();
		}
	}
	protected function checkSession(){
		$status=session_status();
		if($status!=2){
			$this->add_innerlog_line("----- phplogger: WARNING !! - Logger Conf - checkSession(): PHP Session Not Started!");
			return false;
		}
		$this->add_innerlog_line("----- phplogger: checkSession() - Success = ");
		return true;
	}
	protected function nl2br2($string) {
		$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
		return $string;
	}
	protected function txtlog_writeln($ln,$start=0){
		if(!$fh=fopen($this->_logfile,"a")){
			DIE ("Log file Not Writeable - Check permissions...");
		}
		else{
			$this->add_innerlog_line("----- Txtlog := ".$ln);
			if($start===1){$ln="\n".$ln."\n";}else{$ln=$ln."\n";}
			fwrite($fh,$ln);
			fclose($fh);
		}
	}
	protected function mkline($desc,$var,$f,$l){
		$desc=strval($desc);
		if(is_bool($var)){
			if($var){
				$var="TRUE";
			}
			else{
				$var="FALSE";
			}
		}
		$var=print_r($var,true);
		$f=$this->clean_file_location($f);
		$l="Line ".$l;
		$line="";
		$now=date("d-m-Y H:i:s");
		$_SERVER["REMOTE_ADDR"] = array_key_exists( 'REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR']  : '127.0.0.1'; 
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$exploded= explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$ipAddress = array_pop($exploded);
		}
		switch($this->_verbosity){
			case 1:
				$line=$desc." = ".$var;;
			break;
			
			case 2:
				$line="wlog @ Line".$l.": \n".$desc." = ".$var;
				$line="[".$now."] -> ".$line;
			break;
			
			case 3:
				$line="wlog @ ".$f." Line ".$l." : \n".$desc." = ".$var;
				$line="[".$now."] -> [".$ipAddress."]- ".$line;
			break;
		}
		return $line;
	}
	
	protected function add_innerlog_line($stmt){
		if(!is_null($this->_innerlog)){
			$this->_innerlog->addLog_content($stmt);
		}
	}
	
	
	// PUBLIC METHODS
	public function activate(){
		$this->_active=1;
	
		if(isset($GLOBALS["loggerref"])){
			$this->setGlobalactive();
		}
		ini_set('display_errors', 'Off');
		ini_set('log_errors', "On");
		ini_set('error_log', $this->_phperrlogfile);
		error_reporting(E_ALL);
		$this->add_innerlog_line("----- PHP ERR REPORTING Activated =");
		$this->add_innerlog_line("----- phplogger: *** Starting RAM LOG *** =");
	}
	public function deactivate(){
		ini_set('display_errors', 'Off');
		ini_set('log_errors', "Off");
		error_reporting(0);
		$this->add_innerlog_line("----- PHP ERR REPORTING Deactivated =");
		if(isset($GLOBALS["loggerref"])){
			$this->setGlobalactive();
			unset($GLOBALS["loggerref"]);
			$this->add_innerlog_line("----- \$GLOBALS['loggerref'] has been destroyed =");
		}
		$this->add_innerlog_line("----- phplogger: *** Stopping RAM LOG *** =");
	}
	
	public function quietmode(){
		$this->_active=0;
	}
	
	public function php_quietmode(){
		ini_set('display_errors', 'Off');
		ini_set('log_errors', "Off");
		error_reporting(0);
	}
	
	public function innerlogger_activate(){
		if(!is_null($this->_innerlog)){
			$this->_innerlog->activate();
		}
	}
	
	public function innerlogger_deactivate(){
		if(!is_null($this->_innerlog)){
			$this->_innerlog->deactivate();
		}
	}
	
	public function createglobalref(){
		$GLOBALS["loggerref"]=&$this;
	}
	
	public function kill_and_recup(){
		if(!is_null($this->_innerlog)){
			$sessvar=$this->save_ramlog_to_session();
			$this->innerlogger_deactivate();
		}
		$this->_active=0;
		if(isset($sessvar)){
			return $sessvar;
		}
	}
	
	public function kill(){
		if(!is_null($this->_innerlog)){
			$this->add_innerlog_line("----- phplogger: *** Inner Logger DEACTIVATION *** ");
			$this->save_ramlog_to_session();
			$this->innerlogger_deactivate();
		}
		$this->_active=0;
	}
	
	public function txtlog_clear(){
		if($this->_active===1){
			if(!$fh=fopen($this->_logfile,"w")){
				DIE ("Log file Not Writeable - Check permissions...");
			}
			else{
				fclose($fh);
				$this->add_innerlog_line("----- phplogger: Txtlog cleared =");
			}
			if(!$fh=fopen($this->_phperrlogfile,"w")){
				DIE ("Log file Not Writeable - Check permissions...");
			}
			else{
				fclose($fh);
				$this->add_innerlog_line("----- phplogger: Errlog cleared =");
			}
		}
	}
	
	public function txtlog_txt_content(){
		if($this->_active===1){
			if(!$fh=fopen($this->_logfile,"r")){
				DIE ("Log file Not Writeable - Check permissions...");
			}
			else{
				$this->add_innerlog_line("----- phplogger: Txtlog Text Content Sent =");
				return strip_tags(file_get_contents($this->_logfile));
			}
		}
	}
	public function txtlog_html_content(){
		if($this->_active===1){
			if(!$fh=fopen($this->_logfile,"r")){
				DIE ("Log file Not Writeable - Check permissions...");
			}
			else{
				$this->add_innerlog_line("----- phplogger: Txtlog HTML Content Sent =");
				return "<p><span style='width:95%;font-size:".$this->_txtlog_font_size.";font-family:".$this->_txtlog_font_family.";'>".$this->nl2br2(file_get_contents($this->_logfile))."</span></p>";
			}
		}
	}
	public function errlog_txt_content(){
		if($this->_active===1){
			if(!$fh=fopen($this->_phperrlogfile,"r")){
				DIE ("Log file Not Writeable - Check permissions...");
			}
			else{
				$this->add_innerlog_line("----- phplogger: Errlog Text Content Sent =");
				return strip_tags(file_get_contents($this->_phperrlogfile));
			}
		}
	}
	public function errlog_html_content(){
		if($this->_active===1){
			if(!$fh=fopen($this->_phperrlogfile,"r")){
				DIE ("Log file Not Writeable - Check permissions...");
			}
			else{
				$this->add_innerlog_line("----- phplogger: Errlog HTML Content Sent =");
				return "<p><span style='width:95%;font-size:".$this->_txtlog_font_size.";font-family:".$this->_txtlog_font_family.";color:".$this->_php_txt_color."'>".$this->nl2br2(file_get_contents($this->_phperrlogfile))."</span></p>";
			}
		}
	}
	protected function save_ramlog_to_session(){
		if(!$this->checkSession()){
			session_start();
			$this->add_innerlog_line("----- phplogger: WARNING - save_ramlog_to_session() - Forced session start  line =".__LINE__);
		}
		$sessionvarname=$this->_class_name."_classloghtmlcontent";
		$this->add_innerlog_line("----- phplogger: SAVING ramlog to \$_SESSION - save_ramlog_to_session() =");
		if(isset($_SESSION[$sessionvarname])){
			$_SESSION[$sessionvarname].=$this->_innerlog->getHTMLlog_content();
		}
		else{
			$_SESSION[$sessionvarname]=$this->_innerlog->getHTMLlog_content();
		}
		return $_SESSION[$sessionvarname];
	}
	public function start(){
		if($this->_active===1){
			$trace=debug_backtrace();
			$traceinfo=array_shift($trace);
			$location=$this->clean_file_location($traceinfo["file"]);
			$this->txtlog_writeln("<span style=\"color:".$this->_txt_color."\">[</span><span style=\"color:".$this->_debug_color.";\">DEBUG</span><span style=\"color:".$this->_txt_color."\">]   </span><span style=\"color:".$this->_txt_color."\"><b>.............. Starting Log for ".$location."</b></span>",1);
			
		}
	}
	public function stop(){
		if($this->_active===1){
			$trace=debug_backtrace();
			$traceinfo=array_shift($trace);
			$location=$this->clean_file_location($traceinfo["file"]);
			$this->txtlog_writeln("<span style=\"color:".$this->_txt_color."\">[</span><span style=\"color:".$this->_debug_color.";\">DEBUG</span><span style=\"color:".$this->_txt_color."\">]   </span><span style=\"color:".$this->_txt_color."\"><b>.............. End of Log for ".$location."</b></span>");
			
		}
	}
	public function add($desc,$var){
		if($this->_active===1){
			$trace=debug_backtrace();
			$traceinfo=array_shift($trace);
			$ln=$this->mkline($desc,$var,$traceinfo["file"],$traceinfo["line"]);
			$ln="<span style=\"color:".$this->_txt_color."\">".$ln."</span>";
			$this->txtlog_writeln($ln);
		}
	}
	public function info($desc,$var){
		if($this->_active===1){
			$trace=debug_backtrace();
			$traceinfo=array_shift($trace);
			$ln=$this->mkline($desc,$var,$traceinfo["file"],$traceinfo["line"]);
			$ln="<span style=\"color:".$this->_txt_color."\">[</span><span style=\"color:".$this->_info_color.";\">INFO</span><span style=\"color:".$this->_txt_color."\">]    </span><span style=\"color:".$this->_txt_color."\">".$ln."</span>";
			$this->txtlog_writeln($ln);
		}
	}
	public function warning($desc,$var){
		if($this->_active===1){
			$trace=debug_backtrace();
			$traceinfo=array_shift($trace);
			$ln=$this->mkline($desc,$var,$traceinfo["file"],$traceinfo["line"]);
			$ln="<span style=\"color:".$this->_txt_color."\">[</span><span style=\"color:".$this->_warning_color.";\">WARNING</span><span style=\"color:".$this->_txt_color."\">] </span><span style=\"color:".$this->_txt_color."\">".$ln."</span>";
			$this->txtlog_writeln($ln);
		}
	}
	public function error($desc,$var){
		if($this->_active===1){
			$trace=debug_backtrace();
			$traceinfo=array_shift($trace);
			$ln=$this->mkline($desc,$var,$traceinfo["file"],$traceinfo["line"]);
			$ln="<span style=\"color:".$this->_txt_color."\">[</span><span style=\"color:".$this->_error_color.";\">ERROR</span><span style=\"color:".$this->_txt_color."\">]   </span><span style=\"color:".$this->_txt_color."\">".$ln."</span>";
			$this->txtlog_writeln($ln);
		}
	}
	public function fatal($desc,$var){
		if($this->_active===1){
			$trace=debug_backtrace();
			$traceinfo=array_shift($trace);
			$ln=$this->mkline($desc,$var,$traceinfo["file"],$traceinfo["line"]);
			$ln="<span style=\"color:".$this->_txt_color."\">[</span><span style=\"color:".$this->_fatal_color.";\">FATAL</span><span style=\"color:".$this->_txt_color."\">]   </span><span style=\"color:".$this->_txt_color."\">".$ln."</span>";
			$this->txtlog_writeln($ln);
		}
	}
}
?>
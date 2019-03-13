<?php
class classinnerlogger{
	// PROPERTIES
	private $_class_name;
	private $_log_content;
	private $_colors;
	private $_ramlog_color;
	private $_font_size;
	private $_font_family;
	private $_activate;
	
	
	// CONSTANT
	const RAMLOGCOLOR="rgba(255,0,0,1)";
	
	// CONSTRUCTOR
	public function __construct(){
		$this->init_activate();
		$this->setColortab();
		$this->setColors($this->_colors);
		$this->init_log_content();
		$this->init_class_name();
		$this->setFontsize();
		$this->setFontfamily();
		$this->activate();
	}
	
	// INITERS
	protected function init_log_content(){
		$this->_innerlog_content="";
		$this->addLog_content("----- Innerlogger: *** Inner log Initialisation *** ");
	}
	protected function init_class_name(){
		$this->_class_name="classInnerlogger";
		$this->addLog_content("----- Innerlogger: *** Class name Initialisation *** ");
	}
	protected function init_activate(){
		$this->_activate=true;
	}
	
	// SETTERS
	protected function setColortab(){
		$this->_colors=array(
				"RAMLOGCOLOR" => SELF::RAMLOGCOLOR
			);
	}
	
	public function setColors(array $tab){
		foreach($tab as $k=>$v){
			switch($k){
				case "RAMLOGCOLOR":
					$this->_ramlog_color=$v;
					$this->_colors["RAMLOGCOLOR"]=$v;
				break;
			}
		}
		$this->addLog_content("----- Innerlogger: Setting New RAMLOG color table = ".print_r($this->_colors,true));
	}
	
	public function setFontsize($fontsize="14px"){
		$this->_font_size=$fontsize;
		$this->addLog_content("----- Innerlogger:  Setting RAMLOG fontsize = ".print_r($fontsize,true));
	}
	
	public function setFontfamily($fontfam="Times New Roman, Georgia, serif"){
		$this->_font_family=$fontfam;
		$this->addLog_content("----- Innerlogger:  Setting RAMLOG font family = ".print_r($fontfam,true));
	}
	
	// GETTERS
	public function getLog_content(){
		$this->addLog_content("----- Innerlogger: getLog_content() - Raw inner logger text sent = ");
		return strip_tags($this->_log_content);
	}
	
	public function getHTMLlog_content(){
		$this->addLog_content("----- Innerlogger: getHTMLlog_content() - HTML inner logger text sent = ");
		$lg=$this->nl2br2($this->_log_content);
		return "<span style=\"width:95%;font-size:".$this->_font_size.";font-family:".$this->_font_family.";\">".$this->nl2br2($lg)."</span>";
	}
	
	protected function getClassname(){
		$this->addLog_content("----- Innerlogger: getClassname() - RAMLOG Class name sent = ".print_r($this->_class_name,true));
		return $this->_class_name;
		
	}
	
	public function getColors_in_use(){
		$this->addLog_content("----- Innerlogger: getColors_in_use() - RAMLOG colors table sent = ".print_r($this->_colors,true));
		return $this->_colors;
	}
	
	public function getFontsize(){
		$this->addLog_content("----- Innerlogger: getFontsize() - RAMLOG font size sent = ".print_r($this->_font_size,true));
		return $this->_font_size;
	}
	
	public function getFontfamily(){
		$this->addLog_content("----- Innerlogger: getFontfamily() - RAMLOG font family sent = ".print_r($this->_font_family,true));
		return $this->_font_family;
	}
	
	// PROTECTED METHODS
	protected function nl2br2($string) {
		$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
		return $string;
	}
	
	// PUBLIC METHODS
	public function clearLog_content(){
		$this->_log_content="";
	}
	
	public function addLog_content($logstmt){
		if($this->_activate){
			$logstmt=strip_tags(strval($logstmt))."\n";
			$this->_log_content.="<span style=\"color:".$this->_ramlog_color."\">".$logstmt."</span>";
		}
	}
	
	public function activate(){
		$this->_activate=true;
		$this->addLog_content("----- Innerlogger: *** Inner Logger ACTIVATED *** ");
	}
	
	public function deactivate(){
		$this->_activate=false;
	}
}


?>
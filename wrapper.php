<?php

require_once ($CFG->dirroot . '/question/format/smart1/format.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');

class metadataxml_wrapper {
	
	private static $metadataxml_template = "templates/metadata.xml";
	
	private $xml;
	
	public function __construct(){
		global $CFG;
		
		// Load metadata.xml-template.
		$filename = $CFG->dirroot . qformat_smart1::get_plugin_dir() . metadataxml_wrapper::$metadataxml_template;
		$this->xml = load_simplexml($filename);
	}
	
	private function generate_xml() {
		error_logger::get_instance()->log_error("metadataxml_wrapper->generate_xml() called");
		
		// write current date to metadata.xml
		$date = date("Y-m-d\TH:i:s");
		$this->xml->children('lom', true)->lifeCycle->children('smartgallery', true)->creationdatetime = $date;
	}
	
	public function save($dir) {
		$this->generate_xml();
		
		// Write metadata.xml to directory.
		$filename = $dir . "metadata.xml";
		return save_simplexml($this->xml, $filename);
	}
	
}
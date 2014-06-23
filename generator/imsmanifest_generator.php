<?php

require_once ($CFG->dirroot . '/question/format/smart1/format.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');
require_once ($CFG->dirroot . '/question/format/smart1/generator/generator.php');

class imsmanifest_generator extends file_generator {

	private static $template = "generator/templates/imsmanifest.xml";

	private $xml;

	public function __construct(){
		global $CFG;

		// Load settings.xml-template.
		$filename = $CFG->dirroot . qformat_smart1::get_plugin_dir() . imsmanifest_generator::$template;
		$this->xml = load_simplexml($filename);
	}

	protected function generate_xml() {
		error_logger::get_instance()->log_error("simsmanifest_generator->generate_xml() called");

		// Nothing to generate.
	}

	public function save($dir) {
		$this->generate_xml();

		// Write settings.xml to directory.
		$filename = $dir . "imsmanifest.xml";
		return save_simplexml($this->xml, $filename);
	}
	
	public function add_page($page_name) {
		$page = $this->xml->resources->resource[0]->addChild("file");
		$page->addAttribute("href", $page_name);
	
		$page = $this->xml->resources->resource[1]->addChild("file");
		$page->addAttribute("href", $page_name);
	}

}
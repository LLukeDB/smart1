<?php

require_once ($CFG->dirroot . '/question/format/smart1/format.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');

class settingsxml_generator {

	private static $settingsxml_template = "generator/templates/settings.xml";

	private $xml;

	public function __construct(){
		global $CFG;

		// Load settings.xml-template.
		$filename = $CFG->dirroot . qformat_smart1::get_plugin_dir() . settingsxml_generator::$settingsxml_template;
		$this->xml = load_simplexml($filename);
	}

	private function generate_xml() {
		// Nothing to generate.
	}

	public function save($dir) {
		$this->generate_xml();

		// Write settings.xml to directory.
		$filename = $dir . "settings.xml";
		return save_simplexml($this->xml, $filename);
	}

}
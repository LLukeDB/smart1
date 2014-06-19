<?php

require_once ($CFG->dirroot . '/question/format/smart1/format.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');

class metadatardf_wrapper {

	private $xml;

	public function __construct(){

	}

	private function generate_xml() {
		error_logger::get_instance()->log_error("metadatardf_wrapper->generate_xml() called");
		$this->xml = $this->init_metadatardf();
	}

	public function save($dir) {
		$this->generate_xml();

		// Write metadata.rdf to directory.
		$filename = $dir . "metadata.rdf";
		return save_simplexml($this->xml, $filename);
	}
	
	function init_metadatardf() {
		$xml = new SimpleXMLElement("<rdf></rdf>");
		$doc = dom_import_simplexml($xml)->ownerDocument;
		$doc->encoding = 'UTF-8';
		return $xml;
	}

}
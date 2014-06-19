<?php

require_once ($CFG->dirroot . '/question/format/smart1/format.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');

class page_wrapper {

	private $xml;
	private $page_name;
	private $page_id;

	public function __construct($page_name, $page_id){
		$this->page_name = $page_name;
		$this->page_id = $page_id;
		$this->xml = $this->init_page($page_id);
	}

	private function generate_xml() {
		error_logger::get_instance()->log_error("page_wrapper->generate_xml() called");
	}

	public function save($dir) {
		$this->generate_xml();

		// Write pageX.svg to directory.
		$filename = $dir . $this->page_name;
		return save_simplexml($this->xml, $filename);
	}

	function init_page($page_id) {
		$svg = new SimpleXMLElement("<svg></svg>");
		$doc = dom_import_simplexml($svg)->ownerDocument;
		$doc->encoding = 'UTF-8';
		$svg->addAttribute("width", "800");
		$svg->addAttribute("height", "600");
		$svg->addAttribute("xml:id", $page_id, "xml");
		$svg->addChild("title", date("M d-H:s"));
		$g = $svg->addChild("g");
		$g->addAttribute("class", "foreground");
		return $svg;
	}

}
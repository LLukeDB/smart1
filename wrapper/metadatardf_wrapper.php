<?php

require_once ($CFG->dirroot . '/question/format/smart1/format.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');
require_once ($CFG->dirroot . '/question/format/smart1/idgenerator.php');

class metadatardf_wrapper {

	private $xml;
	private $questions;
	
	public function __construct(){
		$this->questions = array();
	}
	
	public function add_question($question) {
		array_push($this->questions, $question);
	}
	
	private function generate_xml() {
		$this->init();
		
		foreach ($this->questions as $question) {
			$this->generate_answer_block($question);
			foreach ($question->choices as $choice) {
				$this->generate_choice_block($choice);
			}
			$this->generate_page_block($question);
		}
		
		return true;
	}

	public function save($dir) {
		$this->generate_xml();

		// Write metadata.rdf to directory.
		$filename = $dir . "metadata.rdf";
		return save_simplexml($this->xml, $filename);
	}
	
	function init() {
		// TODO Add namespaces.
		$xml = new SimpleXMLElement("<rdf></rdf>");
		$doc = dom_import_simplexml($xml)->ownerDocument;
		$doc->encoding = 'UTF-8';
		$this->xml = $xml;
		
		return true;
	}
	
	function generate_answer_block($question) {
		$xml = $this->xml;
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:nodeID", "blank." . $question->answer_block_id);
		$description->addChild("xmlns:senteo:choicevalue", $question->get_true_choice_values());
		
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:nodeID", "blank." . $question->answer_block_id);
		$description->addChild("xmlns:senteo:points", floor($question->points));
		
		return true;
	}
	
	function generate_choice_block($choice) {
		$xml = $this->xml;
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:about", "urn:com.smarttech.notebook:annotation." . $choice->choice_id);
		$description->addChild("xmlns:senteo:assessmentrole", "http://www.smarttech.com/2008/senteo/assessmentrole#choice");
		
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:about", "urn:com.smarttech.notebook:annotation." . $choice->choice_id);
		$description->addChild("xmlns:senteo:choicevalue", $choice->choice_value);
		
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:about", "urn:com.smarttech.notebook:annotation." . $choice->choice_id);
		$type = $description->addChild("xmlns:senteo:type", "");
		$type->addAttribute("xmlns:rdf:resource", "http://www.smarttech.com/2008/notebook/Annotation");
		
		return true;
	}
	
	function generate_page_block($question) {
		$xml = $this->xml;
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:about", "urn:com.smarttech.notebook:page." . $question->page_id);
		$answer = $description->addChild("xmlns:senteo:answer", "");
		$answer->addAttribute("xmlns:rdf:nodeID", "blank." . $question->answer_block_id);
		
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:about", "urn:com.smarttech.notebook:page." . $question->page_id);
		$description->addChild("xmlns:senteo:assessmentrole", "http://www.smarttech.com/2008/senteo/assessmentrole#question");
		
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:about", "urn:com.smarttech.notebook:page." . $question->page_id);
		$description->addChild("xmlns:senteo:choicelabelstyle", "http://www.smarttech.com/2008/senteo/choicelabelstyle#" . $question->choicelabelstyle);
		
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:about", "urn:com.smarttech.notebook:page." . $question->page_id);
		$description->addChild("xmlns:senteo:note", "");
		
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:about", "urn:com.smarttech.notebook:page." . $question->page_id);
		$description->addChild("xmlns:senteo:questionformat", "http://www.smarttech.com/2008/senteo/questionformat#" . $question->questionformat);
		
		$description = $xml->addChild("xmlns:rdf:Description", "");
		$description->addAttribute("xmlns:rdf:about", "urn:com.smarttech.notebook:page." . $question->page_id);
		$type = $description->addChild("xmlns:rdf:type", "");
		$type->addAttribute("xmlns:rdf:resource", "http://www.smarttech.com/2008/notebook/Page");
		
		return true;
	}
}
<?php

require_once ($CFG->dirroot . '/question/format/smart1/format.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');

class page_generator {

	private $xml;
	private $question;

	public function __construct($question){
		$this->question = $question;
		$this->choices = array();
	}
	
	public function set_question($question) {
		$this->question = $question;
	}
	
	public function save($dir) {
		$this->generate_xml();

		// Write pageX.svg to directory.
		$filename = $dir . $this->question->page_name;
		return save_simplexml($this->xml, $filename);
	}

	private function generate_xml() {
		$this->init();
		$this->generate_question();
		for($i = 0; $i < count($this->question->choices); $i++) {
			$this->generate_choice($this->question->choices[$i], $i + 1);
		}
	}

	private function init() {
		$svg = new SimpleXMLElement("<svg></svg>");
		$doc = dom_import_simplexml($svg)->ownerDocument;
		$doc->encoding = 'UTF-8';
		$svg->addAttribute("width", "800");
		$svg->addAttribute("height", "600");
		$svg->addAttribute("xml:id", $this->question->page_id, "xml");
		$svg->addChild("title", date("M d-H:s"));
		$g = $svg->addChild("g");
		$g->addAttribute("class", "foreground");
		
		$this->xml = $svg;
		return true;
	}
	
	private function generate_question() {
		$g = $this->xml->addChild("g");
		$g->addAttribute("class", "question");
		$votemetadata = $g->addChild("votemetadata");
		$questiontext = $votemetadata->addChild("questiontext");
		$questiontext->addAttribute("format", $this->question->format);
		$questiontext->addAttribute("labelstyle", $this->question->labelstyle);
		$questiontext->addAttribute("correct", $this->question->correct);
		$questiontext->addAttribute("points", (String) $this->question->points);
		
		$text = $g->addChild("text");
		$text->addAttribute("transform", "translate(30, 30)");
		$tspan = $text->addChild("tspan");
		$tspan = $tspan->addChild("tspan");
		$tspan = $tspan->addChild("tspan", $this->question->question_num);
		
		$text = $g->addChild("text");
		$text->addAttribute("transform", "translate(61, 30)");
		$tspan = $text->addChild("tspan");
		$tspan = $tspan->addChild("tspan");
		$tspan = $tspan->addChild("tspan", $this->question->questiontext);
		
		return true;
	}
	
	private function generate_choice($choice, $choice_num) {
		$g = $this->xml->addChild("g");
		$g->addAttribute("class", "questionchoice");
		$votemetadata = $g->addChild("votemetadata");
		$choicetext = $votemetadata->addChild("choicetext");
		$choicetext->addAttribute("label", $choice_num);
	
		$text = $g->addChild("text");
		$ypos = ($choice_num - 1) * 60 + 110;
		$text->addAttribute("transform", "translate(81," . $ypos . ")");
		$tspan = $text->addChild("tspan");
		$tspan = $tspan->addChild("tspan");
		$tspan = $tspan->addChild("tspan", $choice->choicetext);
	
		$text = $g->addChild("text");
		$ypos = $ypos + 30;
		$text->addAttribute("transform", "translate(81, 30)");
		$tspan = $text->addChild("tspan");
		$tspan = $tspan->addChild("tspan");
		$tspan = $tspan->addChild("tspan");
		
		return true;
	}

}
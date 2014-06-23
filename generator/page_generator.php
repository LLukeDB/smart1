<?php

require_once ($CFG->dirroot . '/question/format/smart1/format.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');
require_once ($CFG->dirroot . '/question/format/smart1/generator/generator.php');

class page_generator extends file_generator {

	private $xml;
	private $question;

	public function __construct($question){
		$this->question = $question;
		$this->choices = array();
	}
	
	public function save($dir) {
		$this->generate_xml();

		// Write pageX.svg to directory.
		$filename = $dir . $this->question->page_name;
		return save_simplexml($this->xml, $filename);
	}

	protected function generate_xml() {
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
		$g = $this->xml->g->addChild("g");
		$g->addAttribute("class", "question");
		// Write votemetadata.
		$votemetadata = $g->addChild("votemetadata");
		$questiontext = $votemetadata->addChild("questiontext");
		$questiontext->addAttribute("format", $this->question->format);
		$questiontext->addAttribute("labelstyle", $this->question->labelstyle);
		$questiontext->addAttribute("correct", $this->question->correct);
		$questiontext->addAttribute("points", $this->question->points);
		$questiontext->addAttribute("tags", "");
		$questiontext->addAttribute("explanation", $this->question->explanation);
		$questiontext->addAttribute("mathgradingoption", "");
		$questiontext->addAttribute("likert", "");
		
		// Write 1st text-element.
		$text = $g->addChild("text");
		$text->addAttribute("transform", "translate(30, 30)");
		$tspan = $text->addChild("tspan");
		$tspan = $tspan->addChild("tspan");
		$tspan = $tspan->addChild("tspan", $this->question->question_num);
		
		// Write 2nd text-element.
		$text = $g->addChild("text");
		$text->addAttribute("transform", "translate(61, 30)");
		$tspan = $text->addChild("tspan");
		$tspan = $tspan->addChild("tspan");
		$tspan = $tspan->addChild("tspan", $this->question->questiontext);
		
		return true;
	}
	
	private function generate_choice($choice, $choice_num) {
		$g = $this->xml->g->addChild("g");
		$g->addAttribute("class", "questionchoice");
		$votemetadata = $g->addChild("votemetadata");
		$choicetext = $votemetadata->addChild("choicetext");
		$choicetext->addAttribute("label", $choice_num);
	
		$text = $g->addChild("text");
		$ypos = ($choice_num - 1) * 60 + 110;
		$text->addAttribute("transform", "translate(83," . $ypos . ")");
		$tspan = $text->addChild("tspan");
		$tspan = $tspan->addChild("tspan");
		$tspan = $tspan->addChild("tspan", $choice->choicetext);
	
		$text = $g->addChild("text");
		$ypos = $ypos + 30;
		$text->addAttribute("transform", "translate(83, 30)");
		$tspan = $text->addChild("tspan");
		$tspan = $tspan->addChild("tspan");
		$tspan = $tspan->addChild("tspan");
		
		$this->generate_true_false_label($g, $ypos);
		
		return true;
	}
	
	private function generate_true_false_label($parent, $y) {
		$g = $parent->addChild("g");
		$g->addAttribute("class", "group");
		$g->addAttribute("xml:id", "annotation." . id_generator::get_instance()->generate_id(), "xml");
		$g->addAttribute("xbk_transform", "rotate(0.00,66.75,189.31)");
		
		$e = $g->addChild("ellipse");
		$e->addAttribute("cx", "68.01");
		$e->addAttribute("cy", $y);
		$e->addAttribute("rx", "9.49");
		$e->addAttribute("ry", "9.49");
		$e->addAttribute("shapename", "3");
		$e->addAttribute("fill", "#808080");
		$e->addAttribute("st_id", "7");  //??
		$e->addAttribute("stroke", "#808080");
		$e->addAttribute("stroke-width", "1.00");
		$e->addAttribute("fade-time", "6");
		$e->addAttribute("fade-enable", "0");
		$e->addAttribute("metadatatoken", "annotationmetadata/metadata.xml");
		$e->addAttribute("RotationPoint", "(194.000000,329.562500)");
		$e->addAttribute("transform", "rotate(0.00,68.01,190.57)");
		$e->addAttribute("xml:id", "annotation." . id_generator::get_instance()->generate_id(), "xml");
		$e->addAttribute("visible", "1");
		
		$e = $g->addChild("ellipse");
		$e->addAttribute("cx", "65.49");
		$e->addAttribute("cy", $y -2.52);
		$e->addAttribute("rx", "9.49");
		$e->addAttribute("ry", "9.49");
		$e->addAttribute("shapename", "3");
		$e->addAttribute("fill", "#ffffff");
		$e->addAttribute("st_id", "7");  //??
		$e->addAttribute("stroke", "#000000");
		$e->addAttribute("stroke-width", "1.00");
		$e->addAttribute("fade-time", "6");
		$e->addAttribute("fade-enable", "0");
		$e->addAttribute("metadatatoken", "annotationmetadata/metadata.xml");
		$e->addAttribute("RotationPoint", "(194.000000,329.562500)");
		$e->addAttribute("transform", "rotate(0.00,68.01,190.57)");
		$e->addAttribute("xml:id", "annotation." . id_generator::get_instance()->generate_id(), "xml");
		$e->addAttribute("visible", "1");
	}

}
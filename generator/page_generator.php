<?php

require_once ($CFG->dirroot . '/question/format/smart1/format.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');
require_once ($CFG->dirroot . '/question/format/smart1/generator/generator.php');
require_once ($CFG->dirroot . '/question/format/smart1/simplexml_helper.php');

class page_generator extends file_generator {

	private $xml;
	private $question;
	
	private $ypos = 30;

	public function __construct($question){
		$this->question = $question;
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
		$svg->addAttribute("xml:id", "page." . $this->question->page_id, "xml");
		$svg->addChild("title", date("M d-H:s"));
		$g = $svg->addChild("g");
		$g->addAttribute("class", "foreground");
		
		$this->xml = $svg;
		return true;
	}
	
	private function generate_question() {
		$g = $this->xml->g->addChild("g");
		$g->addAttribute("class", "question");
		$g->addAttribute("labelwidth", 16);
		$g->addAttribute("language_direction", 1);
		$g->addAttribute("RotationPoint", "(350.000000,270.000000)"); // TODO Calculate coordiantes.
		$g->addAttribute("transform", "rotate(0.00,160.46,45.64)"); // TODO Calculate coordiantes.
		$g->addAttribute("xml:id", "annotation." . id_generator::get_instance()->generate_id(), "xml");
		$g->addAttribute("visible", 1);
		
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
		$this->generate_text_element($g, $this->question->question_num, 30, false);
		
		// Write 2nd text-element.
		$this->generate_text_element($g, $this->question->questiontext, 60);
		
		return true;
	}
	
	private function generate_choice($choice, $choice_num) {
		$g = $this->xml->g->addChild("g");
		$g->addAttribute("class", "questionchoice");
		$g->addAttribute("xbk_transform", "rotate(0.00,113.57,128.92)"); // TODO Calculate coordinates.
		$g->addAttribute("labelwidth", 66); // TODO Calculate width.
		$g->addAttribute("language_direction", 1);
		$g->addAttribute("RotationPoint", "(376.000000,353.281250)"); 	// TODO Calculate coordinates.
		$g->addAttribute("transform", "rotate(0.00,113.57,128.92)"); 	// TODO Calculate coordinates.
		$g->addAttribute("xml:id", "annotation." . $choice->choice_id, "xml");
		$g->addAttribute("visible", 1);
		
		
		// Write votemetadata.
		$votemetadata = $g->addChild("votemetadata");
		$choicetext = $votemetadata->addChild("choicetext");
		$choicetext->addAttribute("label", $choice_num);
		
		$this->ypos += 25; // Add some vertial extra space.
		$ypos = $this->ypos;
		// Write 1st text-element.
		$this->generate_text_element($g, $choice->choicetext, 83, false);
	
		// Write 2nd text-element.
		$this->generate_text_element($g, "", 100);
		
		$this->generate_true_false_label($g, $ypos + 15);
		
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
	
	private function generate_text_element($parent, $text, $xpos, $new_line=true) {
		$rel_ypos = 0;
		
		// Write text attributes.
		$text_elem = $parent->addChild("text");
		$text_elem->addAttribute("transform", "translate($xpos," . $this->ypos . ") rotate(0.000,32.929,15.641) scale(1.000,1.000)"); // TODO Calculate coordinates.
		$text_elem->addAttribute("RotationPoint", "(346.500000,240.000000)"); // TODO  Calculate coordinates.
		$text_elem->addAttribute("xml:id", "annotation." . id_generator::get_instance()->generate_id(), "xml");
		$text_elem->addAttribute("visible", 1);
		$text_elem->addAttribute("smart-txt-ver", "2.10");
		$text_elem->addAttribute("editwidth", "65.86"); // TODO Calculate width.
		$text_elem->addAttribute("editheight", "31.28"); // TODO Calculate height.
		$text_elem->addAttribute("forcewidth", "0");
		$text_elem->addAttribute("forceheight", "0");
		$text_elem->addAttribute("language_direction", "1");
		$text_elem->addAttribute("textdirection", "0");
		$text_elem->addAttribute("theme_anno_style", "0");
		
		$outer_tspan = $text_elem->addChild("tspan");
		$outer_tspan->addAttribute("justification", "left");
		$outer_tspan->addAttribute("bullet", "0");
		
		// Write text lines.
		$lines = $this->get_rows($text);
		foreach($lines as $line) {
			$rel_ypos += 25;
			$line_tspan = $outer_tspan->addChild("tspan");
			$fragment_tspan = $line_tspan->addChild("tspan", $line);
			$fragment_tspan->addAttribute("fill", "#000000");
			$fragment_tspan->addAttribute("font-size", 28.000);
			$fragment_tspan->addAttribute("font-family", "Arial");
			$fragment_tspan->addAttribute("char-transform", "0.00 1.00 0.00 0.00 0.00 1.00");
			$fragment_tspan->addAttribute("textLength", "100"); // TODO Calculate length.
			$fragment_tspan->addAttribute("x", 0);	// TODO Calculate x.
			$fragment_tspan->addAttribute("y", $rel_ypos);        
		}
		
		// Increase y-position of page if wanted.
		if($new_line) {
			$this->ypos += $rel_ypos;
		}
		
		return true;
	}
	
	// Splits a string in several lines.
	private function get_rows($text) {
		$ntext = strip_tags($text, "<br><p>");
		$ntext = str_replace("\r\n", " ", $ntext);
		$ntext = str_replace("<p>", "", $ntext);
		$rows = preg_split("/<p>|<\/p>|<br \/>/", $ntext);
		
// 		// Remove empty line at the beginning.
// 		if(count($rows) > 0 && trim($rows[0]) == "") {
// 			$rows = array_slice($rows, 1);
// 		}
		
// 		// Remove empty line at the end.
// 		if(count($rows) > 0 && trim($rows[count($rows) - 1]) == "") {
// 			$rows = array_slice($rows, 0, count($rows) - 2);
// 		}
		
		return $rows;
	}
	
	function getTSpanGeometry($tspan) {
		$svg = new SimpleXMLElement("<svg></svg>");
		$svg->addAttribute("width", 1000);
		$svg->addAttribute("height", 1400);
		$text_elem = $svg->addChild("text", "");
		$text_elem->addAttribute("transform", "translate(0, 500)");
		simplexml_append_child($tspan, $text_elem);
	
		$im = new Imagick();
		$im->readimageblob($svg->asXML());
		$im->setImageFormat("png"); // png24
		$im->trimimage(0);
	
		$geometry = $im->getImageGeometry();
		$im->clear();
		$im->destroy();
		return $geometry;
	}
	
	function getTextGeometry($text) {
		$svg = new SimpleXMLElement("<svg></svg>");
		$svg->addAttribute("width", 1000);
		$svg->addAttribute("height", 1400);
		simplexml_append_child($text, $svg);
	
		$im = new Imagick();
		$im->readimageblob($svg->asXML());
		$im->setImageFormat("png"); // png24
		$im->trimimage(0);
	
		$geometry = $im->getImageGeometry();
		$im->clear();
		$im->destroy();
		return $geometry;
	}

}
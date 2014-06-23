<?php

require_once ($CFG->dirroot . '/question/format/smart1/idgenerator.php');

class question {
	// Page infos.
	public $page_id;
	public $page_num;
	public $page_name;
	
	public $answer_block_id;	// needed for rdf
	
	// Question infos.		
	public $format;
	public $questionformat;			// questionformat for rdf, eg. choice
	public $labelstyle;	
	public $choicelabelstyle;		// choicelabelstyle for rdf, eg. true-false
	public $points = "";
	public $question_num;
	public $questiontext;
	public $correct;
	public $question_id;
	public $explanation = "";
	
	public $choices;
	
	public function __construct($page_num) {
		$this->choices = array();
		$this->question_id = id_generator::get_instance()->generate_id();
		$this->page_id = id_generator::get_instance()->generate_id();
		$this->answer_block_id = id_generator::get_instance()->generate_id();
		$this->page_num = $page_num;
		$this->page_name = "page" . $page_num . ".svg";
	}
	
	public function add_choice($choice) {
		array_push($this->choices, $choice);
	}
	
	public function get_true_choice_values() {
		$result = "";
		foreach($this->choices as $choice) {
			if($choice->true == true) {
				$result .= " " . $choice->choice_value;
			}
		}
		return trim($result);
	}
		
}

class choice {
	
	public $choice_id;
	public $choice_value;
	
	public $label;
	public $choicetext;
	public $true;
	
	public function __construct() {
		$this->choice_id = id_generator::get_instance()->generate_id();
		$this->choice_value = id_generator::get_instance()->generate_id();
	}
	
}


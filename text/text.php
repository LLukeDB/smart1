<?php

class text {
	
	private $paragraphs = array();
	private $x;  // Absolute x-coordinate of the top left corner of the text-element.
	private $y;  // Absolute y-coordinate of the top left corner of the text-element.
	
	public function add_paragraph() {
		$p = new paragraph();
		array_push($this->paragraphs, $p);
	}
	
	public function add_textfragment($text, $formattings) {
		if(count($this->paragraphs) == 0) {
			$this->add_paragraph();
		}
		$p = $this->paragraphs[count($this->paragraphs) -1];
		$textfragment = new textfragment($text, $formattings);
		$p->add_textfragment($textfragment);
	}

	public function toString() {
		$s = "<text>";
		foreach ($this->paragraphs as $p) {
			$s .= $p->toString();
		}
		$s .= "\n</text>";
		return $s;
	}
}

class paragraph {
		
	private $lines = array();
	private $textfragments = array();
	
	public function add_textfragment($textfragment) {
		array_push($this->textfragments, $textfragment);
	}
	
	public function toString() {
		$s = "\n<tspan>\n\t<tspan>";
		foreach ($this->textfragments as $tf) {
			$s .= $tf->toString();
		}
		$s .= "\n\t</tspan>\n</tspan>";
		return $s;
	}
}

class line {
	
	private $textfragments = array();
	
}

class textfragment {
	
	public $text;
	public $formattings = array();
	public $x;
	public $y;
	
	public function __construct($text, $formattings) {
		$this->text = $text;
		$this->formattings = $formattings;
	}
	
	public function toString() {
		$s = "\n\t\t<tspan";
		foreach ($this->formattings as $key => $value) {
			$s .= " " . $key . "=\"" . $value . "\"";
		}
		$s .= ">" . $this->text . "</tspan>";
		return $s;
	}
}
